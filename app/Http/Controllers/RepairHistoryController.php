<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\RepairHistory;
use App\Models\RepairPart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RepairHistoryController extends Controller
{
    public function create(Asset $asset)
    {
        return view('repairs.create', [
            'asset' => $asset,
            'technicians' => \App\Models\User::whereIn('role', ['technician', 'admin'])->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request, Asset $asset)
    {
        $validated = $request->validate([
            'reported_date' => 'required|date',
            'issue_description' => 'required|string',
            'maintenance_schedule_id' => 'nullable|exists:maintenance_schedules,id',
            'technician_id' => 'nullable|exists:users,id',
            'parts' => 'nullable|array',
            'parts.*.part_name' => 'required_with:parts|string|max:255',
            'parts.*.quantity' => 'required_with:parts|integer|min:1',
            'parts.*.unit_cost' => 'required_with:parts|numeric|min:0',
        ]);

        DB::transaction(function () use ($validated, $asset) {
            $repair = RepairHistory::create([
                'asset_id' => $asset->id,
                'maintenance_schedule_id' => $validated['maintenance_schedule_id'] ?? null,
                'reported_date' => $validated['reported_date'],
                'issue_description' => $validated['issue_description'],
                'technician_id' => $validated['technician_id'] ?? null,
                'status' => 'reported',
            ]);

            foreach ($validated['parts'] ?? [] as $part) {
                RepairPart::create([
                    'repair_history_id' => $repair->id,
                    'part_name' => $part['part_name'],
                    'quantity' => $part['quantity'],
                    'unit_cost' => $part['unit_cost'],
                ]);
            }

            // Asset goes into repair status
            $asset->update(['status' => 'under_repair']);
        });

        return redirect()->route('assets.show', $asset)->with('success', 'Repair logged.');
    }

    public function complete(Request $request, RepairHistory $repair)
    {
        $validated = $request->validate([
            'repair_date' => 'required|date',
            'cost' => 'required|numeric|min:0',
            'downtime_hours' => 'nullable|numeric|min:0',
            'technician_remarks' => 'nullable|string',
            'status' => 'required|in:completed,unrepairable',
        ]);

        $repair->update($validated);

        // Restore asset to active if repaired, or mark for disposal if unrepairable
        $repair->asset->update([
            'status' => $validated['status'] === 'completed' ? 'active' : 'for_disposal',
        ]);

        return redirect()->route('assets.show', $repair->asset)->with('success', 'Repair record updated.');
    }
}
