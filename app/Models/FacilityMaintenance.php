<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FacilityMaintenance extends Model
{
    protected $fillable = ['facility_item_id', 'due_date', 'status', 'notes', 'completed_date'];

    protected $casts = [
        'due_date' => 'date',
        'completed_date' => 'date',
    ];

    public function item()
    {
        return $this->belongsTo(FacilityItem::class, 'facility_item_id');
    }
}