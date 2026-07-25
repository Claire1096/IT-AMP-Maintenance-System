<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class FacilityMaintenance extends Model
{
    protected $fillable = [
        'facility_item_id', 'maintenance_type', 'priority', 'due_date',
        'scheduled_time', 'technician', 'checklist', 'status', 'notes', 'completed_date',
    ];
    protected $casts = [
        'due_date' => 'date',
        'completed_date' => 'date',
        'checklist' => 'array',
    ];
    public function item()
    {
        return $this->belongsTo(FacilityItem::class, 'facility_item_id');
    }
}