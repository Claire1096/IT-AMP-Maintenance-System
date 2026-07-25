<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\AssetAssignment;
use App\Models\AssetCategory;
use App\Models\AssetMovement;
use App\Models\Department;
use App\Models\Employee;
use App\Models\Location;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use SimpleSoftwareIO\QrCode\Facades\QrCode; // composer require simplesoftwareio/simple-qrcode

class AssetController extends Controller
{
    public function index(Request $request)
    {
        $assets = Asset::query()
            ->with(['category', 'assignedEmployee', 'department', 'location', 'supplier'])
            ->when($request->status, fn ($q) => $q->where('status', $request->status))
            ->when($request->category_id, fn ($q) => $q->where('category_id', $request->category_id))
            ->when($request->department_id, fn ($q) => $q->where('department_id', $request->department_id))
            ->when($request->location_id, fn ($q) => $q->where('location_id', $request->location_id))
            ->when($request->search, function ($q) use ($request) {
                $q->where(function ($sub) use ($request) {
                    $sub->where('asset_tag', 'like', "%{$request->search}%")
                        ->orWhere('name', 'like', "%{$request->search}%")
                        ->orWhere('serial_number', 'like', "%{$request->search}%");
                });
            })
            ->latest()
            ->paginate(20);

        $stats = [
            'total' => Asset::count(),
            'active' => Asset::where('status', 'active')->count(),
            'under_repair' => Asset::where('status', 'under_repair')->count(),
            'expiring_soon' => Asset::whereNotNull('warranty_expiration')
                ->whereBetween('warranty_expiration', [now(), now()->addDays(30)])
                ->count(),
        ];

        return view('assets.index', [
            'assets' => $assets,
            'stats' => $stats,
            'categories' => AssetCategory::all(),
            'departments' => Department::all(),
            'locations' => Location::with('building')->get(),
        ]);
    }

  public function create()
{
    return view('assets.create', [
        'categories' => AssetCategory::all(),
        'employees' => Employee::where('is_active', true)->orderBy('first_name')->get(),
        'departments' => Department::all(),
        'locations' => Location::with('building')->get(),
        'suppliers' => Supplier::all(),
        'positions' => \App\Models\Position::orderBy('department_id')->get(),
    ]);
}
    

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:asset_categories,id',
            'brand' => 'nullable|string|max:255',
            'model' => 'nullable|string|max:255',
            'serial_number' => 'nullable|string|max:255|unique:assets,serial_number',
            'assigned_employee_id' => 'nullable|exists:employees,id',
            'department_id' => 'nullable|exists:departments,id',
            'location_id' => 'nullable|exists:locations,id',
            'purchase_date' => 'nullable|date',
            'purchase_cost' => 'nullable|numeric|min:0',
            'warranty_expiration' => 'nullable|date|after_or_equal:purchase_date',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'description' => 'nullable|string',
        ]);

        $validated['asset_tag'] = $this->generateAssetTag($validated['category_id']);
        $validated['status'] = 'active';

        $asset = Asset::create($validated);
        $this->generateQrCode($asset);

        if ($asset->assigned_employee_id) {
            AssetAssignment::create([
                'asset_id' => $asset->id,
                'employee_id' => $asset->assigned_employee_id,
                'department_id' => $asset->department_id,
                'assigned_by' => auth()->id(),
                'assigned_date' => now(),
            ]);
        }

        return redirect()->route('assets.show', $asset)->with('success', 'Asset registered successfully.');
    }

    public function show(Asset $asset)
    {
        $asset->load([
            'category', 'assignedEmployee', 'department', 'location', 'supplier',
            'assignments.employee', 'movements.fromLocation', 'movements.toLocation',
            'maintenanceSchedules' => fn ($q) => $q->latest('scheduled_date'),
            'repairHistories' => fn ($q) => $q->latest('reported_date'),
        ]);

        $employees = \App\Models\Employee::where('is_active', true)->orderBy('first_name')->get();
        $departments = \App\Models\Department::all();

        return view('assets.show', compact('asset', 'employees', 'departments'));
    }

    public function edit(Asset $asset)
    {
        return view('assets.edit', [
            'asset' => $asset,
            'categories' => AssetCategory::all(),
            'employees' => Employee::where('is_active', true)->orderBy('first_name')->get(),
            'departments' => Department::all(),
            'locations' => Location::with('building')->get(),
            'suppliers' => Supplier::all(),
        ]);
    }

    public function update(Request $request, Asset $asset)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'brand' => 'nullable|string|max:255',
            'model' => 'nullable|string|max:255',
            'serial_number' => 'nullable|string|max:255|unique:assets,serial_number,' . $asset->id,
            'department_id' => 'nullable|exists:departments,id',
            'location_id' => 'nullable|exists:locations,id',
            'purchase_date' => 'nullable|date',
            'purchase_cost' => 'nullable|numeric|min:0',
            'warranty_expiration' => 'nullable|date',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'status' => 'required|in:active,under_repair,for_disposal,lost',
            'description' => 'nullable|string',
        ]);

        // Log a movement if location changed
        if ($asset->location_id != ($validated['location_id'] ?? null)) {
            AssetMovement::create([
                'asset_id' => $asset->id,
                'from_location_id' => $asset->location_id,
                'to_location_id' => $validated['location_id'] ?? null,
                'moved_by' => auth()->id(),
                'moved_at' => now(),
            ]);
        }

        $asset->update($validated);

        return redirect()->route('assets.show', $asset)->with('success', 'Asset updated.');
    }

    /**
     * Reassign an asset to a different employee (creates assignment history record).
     */
    public function reassign(Request $request, Asset $asset)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'department_id' => 'nullable|exists:departments,id',
            'remarks' => 'nullable|string',
        ]);

        // Close out previous assignment
        $asset->assignments()->whereNull('returned_date')->update(['returned_date' => now()]);

        AssetAssignment::create([
            'asset_id' => $asset->id,
            'employee_id' => $validated['employee_id'],
            'department_id' => $validated['department_id'] ?? null,
            'assigned_by' => auth()->id(),
            'assigned_date' => now(),
            'remarks' => $validated['remarks'] ?? null,
        ]);

        $asset->update([
            'assigned_employee_id' => $validated['employee_id'],
            'department_id' => $validated['department_id'] ?? $asset->department_id,
        ]);

        return back()->with('success', 'Asset reassigned successfully.');
    }

    public function destroy(Asset $asset)
    {
        $asset->delete(); // soft delete

        return redirect()->route('assets.index')->with('success', 'Asset removed.');
    }

    // --- Helpers ---

    private function generateAssetTag(int $categoryId): string
    {
        $category = AssetCategory::findOrFail($categoryId);
        $prefix = $category->prefix ?: strtoupper(substr($category->name, 0, 3));
        $year = now()->year;

        $count = Asset::where('category_id', $categoryId)
            ->whereYear('created_at', $year)
            ->count() + 1;

        return sprintf('%s-%d-%04d', $prefix, $year, $count);
    }

    private function generateQrCode(Asset $asset): void
    {
        $path = "qrcodes/{$asset->asset_tag}.svg";
        $qr = QrCode::size(300)->generate(route('assets.show', $asset));
        \Storage::disk('public')->put($path, $qr);
        $asset->update(['qr_code_path' => $path]);
    }
}

