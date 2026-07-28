<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DamageReport extends Model
{
    protected $fillable = [
        'report_number', 'category', 'facility_item_id', 'asset_id',
        'asset_name', 'asset_type', 'asset_tag_no', 'date_reported',
        'date_of_incident', 'time_of_incident', 'type_of_incident',
        'cause_of_damage', 'cause_other_note', 'description', 'action_taken',
        'inspected_by', 'inspection_date', 'condition', 'estimated_cost',
        'remarks', 'facilitator_name', 'facilitator_date',
    ];

    protected $casts = [
        'date_reported' => 'date',
        'date_of_incident' => 'date',
        'inspection_date' => 'date',
        'facilitator_date' => 'date',
        'estimated_cost' => 'decimal:2',
    ];

    public function facilityItem(): BelongsTo
    {
        return $this->belongsTo(FacilityItem::class);
    }

    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class);
    }
}