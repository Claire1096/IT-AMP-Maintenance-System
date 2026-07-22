<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\MaintenanceChecklistItem;
use App\Models\MaintenanceSchedule;
use Illuminate\Http\Request;

class MaintenanceScheduleController extends Controller
{
    public function index(Request $request)
    {
        $schedules = MaintenanceSchedule::query()
            ->with(['asset', 'technician'])
            ->when($request->status, fn ($q) => $q->where('status', $request->status))
            ->when($request->due_this_month, function ($q) {
                $q->whereMonth('next_maintenance_date', now()->month)
                  ->whereYear('next_maintenance_date', now()->year);
            })
            ->orderBy('next_maintenance_date')
            ->paginate(20);

        return view('maintenance.index', compact('schedules'));
    }

    public function create(Asset $asset)
    {
        return view('maintenance.create', [
           'asset' => $asset,
           'technicians' => \App\Models\User::whereIn('role', ['technician', 'admin'])->orderBy('name')->get()
] );
    }

    public function store(Request $request, Asset $asset)
    {
        $validated = $request->validate([
            'maintenance_type' => 'required|string|max:255',
            'frequency' => 'required|in:one_time,monthly,quarterly,semi_annual,annual',
            'scheduled_date' => 'required|date',
            'assigned_technician_id' => 'nullable|exists:users,id',
            'checklist' => 'nullable|array',
            'checklist.*' => 'string|max:255',
        ]);

        $schedule = MaintenanceSchedule::create([
            'asset_id' => $asset->id,
            'maintenance_type' => $validated['maintenance_type'],
            'frequency' => $validated['frequency'],
            'scheduled_date' => $validated['scheduled_date'],
            'next_maintenance_date' => $validated['scheduled_date'],
            'assigned_technician_id' => $validated['assigned_technician_id'] ?? null,
            'status' => 'scheduled',
        ]);

        foreach ($validated['checklist'] ?? [] as $i => $task) {
            MaintenanceChecklistItem::create([
                'maintenance_schedule_id' => $schedule->id,
                'task_description' => $task,
                'sort_order' => $i,
            ]);
        }

        return redirect()->route('assets.show', $asset)->with('success', 'Maintenance scheduled.');
    }

    /**
     * Mark a scheduled maintenance as completed, roll forward the next date if recurring.
     */
    public function complete(Request $request, MaintenanceSchedule $schedule)
    {
        $validated = $request->validate([
            'technician_remarks' => 'nullable|string',
            'checklist' => 'nullable|array', // [item_id => bool]
        ]);

        foreach ($validated['checklist'] ?? [] as $itemId => $checked) {
            MaintenanceChecklistItem::where('id', $itemId)
                ->where('maintenance_schedule_id', $schedule->id)
                ->update(['is_completed' => (bool) $checked]);
        }

        $nextDate = $schedule->calculateNextDate(now());

        $schedule->update([
            'status' => 'completed',
            'technician_remarks' => $validated['technician_remarks'] ?? $schedule->technician_remarks,
            'completed_at' => now(),
        ]);

        // If recurring, create the next occurrence automatically
        if ($nextDate) {
            $next = $schedule->replicate(['status', 'completed_at', 'technician_remarks']);
            $next->scheduled_date = $nextDate;
            $next->next_maintenance_date = $nextDate;
            $next->status = 'scheduled';
            $next->save();
        }

        return back()->with('success', 'Maintenance marked as completed.');
    }

    /**
     * Flip any schedule whose next_maintenance_date has passed to 'overdue'.
     * Intended to be called from a scheduled command (see console/Kernel).
     */
    public static function flagOverdue(): int
    {
        return MaintenanceSchedule::where('status', 'scheduled')
            ->whereDate('next_maintenance_date', '<', now())
            ->update(['status' => 'overdue']);
    }
}

