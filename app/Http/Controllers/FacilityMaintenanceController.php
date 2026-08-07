<?php
namespace App\Http\Controllers;
use App\Models\FacilityItem;
use App\Models\FacilityMaintenance;
use Illuminate\Http\Request;
class FacilityMaintenanceController extends Controller
{
    private array $maintenanceTypes = ['preventive', 'corrective', 'emergency', 'improvement'];
    private array $priorities = ['low', 'high', 'critical'];
    private array $checklistItems = [
        'Check hardware condition',
        'Clean internal components',
        'Check power cable and adapters',
        'Update software / OS',
        'Verify system performance',
        'Backup important data',
    ];
    public function index(Request $request)
    {
        FacilityMaintenance::where('status', 'pending')
        ->get()
        ->each(function ($maintenance) {
            $deadline = $maintenance->scheduled_time
                ? \Carbon\Carbon::parse($maintenance->due_date->format('Y-m-d') . ' ' . $maintenance->scheduled_time)
                : $maintenance->due_date->endOfDay();
            if (now()->greaterThan($deadline)) {
                $maintenance->update(['status' => 'overdue']);
        }
    });    
        $maintenances = FacilityMaintenance::query()
            ->with('item')
            ->when($request->status, fn ($q) => $q->where('status', $request->status))
            ->orderBy('due_date')
            ->paginate(12);
        $stats = [
            'pending' => FacilityMaintenance::where('status', 'pending')->count(),
            'overdue' => FacilityMaintenance::where('status', 'overdue')->count(),
            'done' => FacilityMaintenance::where('status', 'done')->count(),
        ];
        return view('facility-maintenance.index', [
            'maintenances' => $maintenances,
            'stats' => $stats,
        ]);
    }
    public function create(Request $request)
    {
        $selectedItemId = $request->query('item');
        return view('facility-maintenance.create', [
            'items' => FacilityItem::orderBy('name')->get(),
            'selectedItemId' => $selectedItemId,
            'maintenanceTypes' => $this->maintenanceTypes,
            'priorities' => $this->priorities,
            'checklistItems' => $this->checklistItems,
        ]);
    }
    public function store(Request $request)
    {
        $validated = $request->validate([
            'facility_item_id' => 'required|exists:facility_items,id',
            'maintenance_type' => 'required|in:' . implode(',', $this->maintenanceTypes),
            'priority' => 'required|in:' . implode(',', $this->priorities),
            'due_date' => 'required|date',
            'scheduled_time' => 'nullable|date_format:H:i',
            'technician' => 'nullable|string|max:255',
            'checklist' => 'nullable|array',
            'checklist.*' => 'string',
            'notes' => 'nullable|string',
        ]);
        $validated['status'] = 'pending';
        $maintenance = FacilityMaintenance::create($validated);
        return redirect()->route('facility-maintenance.index')->with('success', 'Maintenance scheduled.');
    }
    public function edit(FacilityMaintenance $facilityMaintenance)
    {
        return view('facility-maintenance.edit', [
            'maintenance' => $facilityMaintenance,
            'items' => FacilityItem::orderBy('name')->get(),
            'maintenanceTypes' => $this->maintenanceTypes,
            'priorities' => $this->priorities,
            'checklistItems' => $this->checklistItems,
        ]);
    }
    public function update(Request $request, FacilityMaintenance $facilityMaintenance)
    {
        $validated = $request->validate([
            'facility_item_id' => 'required|exists:facility_items,id',
            'maintenance_type' => 'required|in:' . implode(',', $this->maintenanceTypes),
            'priority' => 'required|in:' . implode(',', $this->priorities),
            'due_date' => 'required|date',
            'scheduled_time' => 'nullable|date_format:H:i',
            'technician' => 'nullable|string|max:255',
            'checklist' => 'nullable|array',
            'checklist.*' => 'string',
            'notes' => 'nullable|string',
        ]);
        $facilityMaintenance->update($validated);
        return redirect()->route('facility-maintenance.index')->with('success', 'Maintenance schedule updated.');
    }
    public function show(FacilityMaintenance $facilityMaintenance)
    {
        $facilityMaintenance->load('item');
        return view('facility-maintenance.show', ['maintenance' => $facilityMaintenance]);
    }
    public function destroy(FacilityMaintenance $facilityMaintenance)
    {
        $facilityMaintenance->delete();
        return redirect()->route('facility-maintenance.index')->with('success', 'Maintenance schedule removed.');
    }
    public function complete(FacilityMaintenance $facilityMaintenance)
    {
        $facilityMaintenance->update([
            'status' => 'done',
            'completed_date' => now(),
        ]);
        return back()->with('success', 'Maintenance marked complete.');
    }
}
