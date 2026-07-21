<?php

namespace App\Notifications;

use App\Models\MaintenanceSchedule;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class MaintenanceDueNotification extends Notification
{
    use Queueable;

    public function __construct(public MaintenanceSchedule $schedule) {}

    public function via($notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable): MailMessage
    {
        $asset = $this->schedule->asset;

        return (new MailMessage)
            ->subject("Maintenance Due: {$asset->asset_tag} - {$asset->name}")
            ->greeting("Hi {$notifiable->name},")
            ->line("A preventive maintenance task is due for {$asset->name} ({$asset->asset_tag}).")
            ->line("Type: {$this->schedule->maintenance_type}")
            ->line("Due date: {$this->schedule->next_maintenance_date->format('M d, Y')}")
            ->action('View Asset', route('assets.show', $asset))
            ->line('Please complete or update this maintenance task in the system.');
    }

    public function toArray($notifiable): array
    {
        return [
            'schedule_id' => $this->schedule->id,
            'asset_id' => $this->schedule->asset_id,
            'message' => "Maintenance due for {$this->schedule->asset->asset_tag}",
        ];
    }
}

