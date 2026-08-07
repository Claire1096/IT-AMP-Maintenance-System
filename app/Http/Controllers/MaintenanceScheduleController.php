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
            ->paginate(12);

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

    public function edit(MaintenanceSchedule $schedule)
    {
        $schedule->load('checklistItems');

        return view('maintenance.edit', [
            'schedule' => $schedule,
            'technicians' => \App\Models\User::whereIn('role', ['technician', 'admin'])->orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, MaintenanceSchedule $schedule)
    {
        $validated = $request->validate([
            'maintenance_type' => 'required|string|max:255',
            'frequency' => 'required|in:one_time,monthly,quarterly,semi_annual,annual',
            'scheduled_date' => 'required|date',
            'assigned_technician_id' => 'nullable|exists:users,id',
            'checklist_items' => 'nullable|array',
            'checklist_items.*.task_description' => 'nullable|string|max:255',
            'checklist_items.*._delete' => 'nullable|boolean',
            'new_checklist' => 'nullable|array',
            'new_checklist.*' => 'nullable|string|max:255',
        ]);

        $schedule->update([
            'maintenance_type' => $validated['maintenance_type'],
            'frequency' => $validated['frequency'],
            'scheduled_date' => $validated['scheduled_date'],
            'next_maintenance_date' => $validated['scheduled_date'],
            'assigned_technician_id' => $validated['assigned_technician_id'] ?? null,
        ]);

        foreach ($validated['checklist_items'] ?? [] as $itemId => $itemData) {
            if (!empty($itemData['_delete'])) {
                MaintenanceChecklistItem::where('id', $itemId)
                    ->where('maintenance_schedule_id', $schedule->id)
                    ->delete();
            } elseif (!empty($itemData['task_description'])) {
                MaintenanceChecklistItem::where('id', $itemId)
                    ->where('maintenance_schedule_id', $schedule->id)
                    ->update(['task_description' => $itemData['task_description']]);
            }
        }

        $sortOrder = ($schedule->checklistItems()->max('sort_order') ?? -1) + 1;
        foreach ($validated['new_checklist'] ?? [] as $task) {
            if (trim((string) $task) !== '') {
                MaintenanceChecklistItem::create([
                    'maintenance_schedule_id' => $schedule->id,
                    'task_description' => $task,
                    'sort_order' => $sortOrder++,
                ]);
            }
        }

        return redirect()->route('maintenance.index')->with('success', 'Maintenance schedule updated.');
    }

    public function show(MaintenanceSchedule $schedule)
    {
        $schedule->load(['asset', 'technician', 'checklistItems']);

        return view('maintenance.show', ['schedule' => $schedule]);
    }

    public function destroy(MaintenanceSchedule $schedule)
    {
        $schedule->delete();

        return redirect()->route('maintenance.index')->with('success', 'Maintenance schedule removed.');
    }
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
