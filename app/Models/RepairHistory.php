<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RepairHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'asset_id', 'maintenance_schedule_id', 'reported_date', 'issue_description',
        'repair_date', 'technician_id', 'cost', 'downtime_hours', 'status', 'technician_remarks',
    ];

    protected $casts = [
        'reported_date' => 'date',
        'repair_date' => 'date',
        'cost' => 'decimal:2',
        'downtime_hours' => 'decimal:2',
    ];

    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class);
    }

    public function maintenanceSchedule(): BelongsTo
    {
        return $this->belongsTo(MaintenanceSchedule::class);
    }

    public function technician(): BelongsTo
    {
        return $this->belongsTo(User::class, 'technician_id');
    }

    public function parts(): HasMany
    {
        return $this->hasMany(RepairPart::class);
    }

    public function getTotalPartsCostAttribute(): float
    {
        return (float) $this->parts()->sum(\DB::raw('quantity * unit_cost'));
    }
}

