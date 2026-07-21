<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RepairPart extends Model
{
    use HasFactory;

    protected $fillable = ['repair_history_id', 'part_name', 'quantity', 'unit_cost'];

    protected $casts = [
        'unit_cost' => 'decimal:2',
    ];

    public function repairHistory(): BelongsTo
    {
        return $this->belongsTo(RepairHistory::class);
    }

    public function getSubtotalAttribute(): float
    {
        return (float) $this->quantity * (float) $this->unit_cost;
    }
}

