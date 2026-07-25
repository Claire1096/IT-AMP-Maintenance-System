<?php

namespace App\Http\Controllers;

use App\Models\FacilityItem;
use App\Models\FacilityMaintenance;
use Illuminate\Http\Request;

class FacilityMaintenanceController extends Controller
{
    public function index(Request $request)
    {
        $maintenances = FacilityMaintenance::query()
            ->with('item')
            ->when($request->status, fn ($q) => $q->where('status', $request->status))
            ->orderBy('due_date')
            ->paginate(20);

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

    public function create(FacilityItem $facilityItem)
    {
        return view('facility-maintenance.create', ['item' => $facilityItem]);
    }

    public function store(Request $request, FacilityItem $facilityItem)
    {
        $validated = $request->validate([
            'due_date' => 'required|date',
            'notes' => 'nullable|string',
        ]);

        $validated['facility_item_id'] = $facilityItem->id;
        $validated['status'] = 'pending';

        FacilityMaintenance::create($validated);

        return redirect()->route('facility-items.show', $facilityItem)->with('success', 'Maintenance scheduled.');
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