<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\FacilityItem;
use App\Models\Location;
use App\Models\Supplier;
use Illuminate\Http\Request;
use App\Models\FacilityMaintenance;

class FacilityItemController extends Controller
{
    private array $categories = ['Facility and Maintenance'];
    private array $assetTypes = ['tools', 'supplies', 'equipment', 'electronics', 'furniture', 'vehicles', 'machinery'];
    private array $buildingStructures = ['doors', 'windows', 'walls', 'flooring', 'light', 'switch', 'outlet', 'roof', 'gate', 'water tank'];
    private array $statuses = ['in_use', 'in_storage', 'damaged', 'disposed', 'missing'];
    private array $conditions = ['good', 'fair', 'poor'];

    public function index(Request $request)
{
    $items = FacilityItem::query()
        ->with(['department', 'location', 'maintenances' => fn ($q) => $q->where('status', 'overdue')])
        ->when($request->category, fn ($q) => $q->where('category', $request->category))
        ->when($request->asset_type, fn ($q) => $q->where('asset_type', $request->asset_type))
        ->when($request->status, fn ($q) => $q->where('status', $request->status))
        ->when($request->department_id, fn ($q) => $q->where('department_id', $request->department_id))
        ->when($request->location_id, fn ($q) => $q->where('location_id', $request->location_id))
        ->when($request->search, function ($q) use ($request) {
            $q->where(function ($sub) use ($request) {
                $sub->where('item_tag', 'like', "%{$request->search}%")
                    ->orWhere('name', 'like', "%{$request->search}%");
            });
        })
        ->latest()
        ->simplePaginate(10)
        ->withQueryString();

    $stats = [
        'total' => FacilityItem::count(),
        'in_use' => FacilityItem::where('status', 'in_use')->count(),
        'in_storage' => FacilityItem::where('status', 'in_storage')->count(),
        'damaged' => FacilityItem::where('status', 'damaged')->count(),
    ];

    $maintenances = FacilityMaintenance::with('item')
        ->where('status', '!=', 'done')
        ->orderBy('due_date')
        ->paginate(10);

    // Live search / filter / pagination requests: return just the stats+table+pager markup.
    if ($request->ajax()) {
        return view('facility-items._results', compact('items', 'stats'))->render();
    }

    return view('facility-items.index', [
        'items' => $items,
        'stats' => $stats,
        'departments' => Department::all(),
        'locations' => Location::with('building')->get(),
        'categories' => $this->categories,
        'statuses' => $this->statuses,
        'assetTypes' => $this->assetTypes,
    ]);
}

public function create()
{
    return view('facility-items.create', [
        'departments' => Department::all(),
        'buildings' => \App\Models\Building::orderBy('name')->get(),
        'locations' => Location::with('building')->get(),
        'suppliers' => Supplier::all(),
        'categorySuggestions' => $this->categories,
        'conditions' => $this->conditions,
        'statuses' => $this->statuses,
        'assetTypes' => $this->assetTypes,
        'buildingStructures' => $this->buildingStructures,
    ]);
}
   public function store(Request $request)
{
    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'brand' => 'nullable|string|max:255',
        'category' => 'required|in:Facility and Maintenance',
        'description' => 'nullable|string|max:1000',
        'quantity' => 'required|integer|min:1',
        'department_id' => 'nullable|exists:departments,id',
        'location_id' => 'nullable|exists:locations,id',
        'condition' => 'required|in:' . implode(',', $this->conditions),
        'status' => 'required|in:' . implode(',', $this->statuses),
        'purchase_date' => 'nullable|date',
        'purchase_cost' => 'nullable|numeric|min:0',
        'supplier_id' => 'nullable|exists:suppliers,id',
        'asset_type' => 'nullable|in:' . implode(',', $this->assetTypes),
        'building_structure' => 'nullable|in:' . implode(',', $this->buildingStructures),
    ]);

    $validated['item_tag'] = $this->generateItemTag($validated['category']);
    

    if ($validated['status'] === 'missing') {
        $validated['missing_since'] = now();
    }

    $item = FacilityItem::create($validated);
    $this->generateQrCode($item);

    return redirect()->route('facility-items.show', $item)->with('success', 'Facility item registered successfully.');
}
    public function show(FacilityItem $facilityItem)
    {
        $facilityItem->load(['department', 'location', 'supplier']);

        return view('facility-items.show', ['item' => $facilityItem]);
    }
    private function generateQrCode(FacilityItem $item): void
{
    $path = "qrcodes/facility-{$item->item_tag}.svg";
    $qr = \SimpleSoftwareIO\QrCode\Facades\QrCode::size(300)->generate(route('facility-items.show', $item));
    \Storage::disk('public')->put($path, $qr);
    $item->update(['qr_code_path' => $path]);
}

  public function edit(FacilityItem $facilityItem)
{
    return view('facility-items.edit', [
        'item' => $facilityItem,
        'departments' => Department::all(),
        'buildings' => \App\Models\Building::orderBy('name')->get(),
        'locations' => Location::with('building')->get(),
        'suppliers' => Supplier::all(),
        'categories' => $this->categories,
        'conditions' => $this->conditions,
        'statuses' => $this->statuses,
    ]);
}

    public function update(Request $request, FacilityItem $facilityItem)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|in:' . implode(',', $this->categories),
            'description' => 'nullable|string|max:1000',
            'quantity' => 'required|integer|min:1',
            'department_id' => 'nullable|exists:departments,id',
            'location_id' => 'nullable|exists:locations,id',
            'condition' => 'required|in:' . implode(',', $this->conditions),
            'status' => 'required|in:' . implode(',', $this->statuses),
            'purchase_date' => 'nullable|date',
            'purchase_cost' => 'nullable|numeric|min:0',
            'supplier_id' => 'nullable|exists:suppliers,id',
        ]);

        if ($validated['status'] === 'missing' && $facilityItem->status !== 'missing') {
            $validated['missing_since'] = now();
        } elseif ($validated['status'] !== 'missing') {
            $validated['missing_since'] = null;
        }

        $facilityItem->update($validated);

        

        return redirect()->route('facility-items.show', $facilityItem)->with('success', 'Facility item updated.');
    }

    public function destroy(FacilityItem $facilityItem)
    {
        $facilityItem->delete();

        return redirect()->route('facility-items.index')->with('success', 'Facility item removed.');
    }

    private function generateItemTag(string $category): string
{
    $prefix = strtoupper(substr(preg_replace('/[^A-Za-z]/', '', $category), 0, 3)) ?: 'GEN';
    $year = now()->year;

    $count = FacilityItem::withTrashed()
        ->where('item_tag', 'like', "FAC-{$prefix}-{$year}-%")
        ->count() + 1;

    return sprintf('FAC-%s-%d-%04d', $prefix, $year, $count);
}
}