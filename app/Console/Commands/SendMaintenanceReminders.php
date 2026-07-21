<?php

namespace App\Console\Commands;

use App\Models\MaintenanceSchedule;
use App\Notifications\MaintenanceDueNotification;
use Illuminate\Console\Command;

class SendMaintenanceReminders extends Command
{
    protected $signature = 'maintenance:remind';
    protected $description = 'Flag overdue maintenance and email technicians about upcoming (7-day) maintenance';

    public function handle(): int
    {
        // 1. Flag anything past due
        $overdueCount = MaintenanceSchedule::where('status', 'scheduled')
            ->whereDate('next_maintenance_date', '<', now())
            ->update(['status' => 'overdue']);

        $this->info("Flagged {$overdueCount} schedule(s) as overdue.");

        // 2. Notify technicians about maintenance due within the next 7 days
        $upcoming = MaintenanceSchedule::with(['asset', 'technician'])
            ->whereIn('status', ['scheduled', 'overdue'])
            ->whereDate('next_maintenance_date', '<=', now()->addDays(7))
            ->get();

        foreach ($upcoming as $schedule) {
            if ($schedule->technician) {
                $schedule->technician->notify(new MaintenanceDueNotification($schedule));
            }
        }

        $this->info("Sent {$upcoming->count()} reminder notification(s).");

        return self::SUCCESS;
    }
}

