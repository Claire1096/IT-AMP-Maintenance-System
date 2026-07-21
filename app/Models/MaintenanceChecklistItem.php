<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MaintenanceChecklistItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'maintenance_schedule_id', 'task_description', 'is_completed', 'notes', 'sort_order',
    ];

    protected $casts = [
        'is_completed' => 'boolean',
    ];

    public function schedule(): BelongsTo
    {
        return $this->belongsTo(MaintenanceSchedule::class, 'maintenance_schedule_id');
    }
}

