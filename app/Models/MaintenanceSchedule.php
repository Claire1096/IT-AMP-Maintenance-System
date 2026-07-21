<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MaintenanceSchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'asset_id', 'maintenance_type', 'frequency', 'scheduled_date',
        'next_maintenance_date', 'assigned_technician_id', 'status',
        'technician_remarks', 'completed_at',
    ];

    protected $casts = [
        'scheduled_date' => 'date',
        'next_maintenance_date' => 'date',
        'completed_at' => 'datetime',
    ];

    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class);
    }

    public function technician(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_technician_id');
    }

    public function checklistItems(): HasMany
    {
        return $this->hasMany(MaintenanceChecklistItem::class);
    }

    public function repairHistories(): HasMany
    {
        return $this->hasMany(RepairHistory::class);
    }

    /**
     * Compute the next maintenance date based on frequency, from a given base date.
     */
    public function calculateNextDate(\DateTimeInterface $from): ?\Carbon\Carbon
    {
        $date = \Carbon\Carbon::parse($from);

        return match ($this->frequency) {
            'monthly' => $date->copy()->addMonth(),
            'quarterly' => $date->copy()->addMonths(3),
            'semi_annual' => $date->copy()->addMonths(6),
            'annual' => $date->copy()->addYear(),
            default => null, // one_time has no next date
        };
    }
}

