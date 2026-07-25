<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FacilityItem extends Model
{
    use HasFactory, SoftDeletes;

  protected $fillable = [
    'item_tag', 'name', 'category', 'description', 'quantity',
    'department_id', 'location_id', 'condition', 'status',
    'purchase_date', 'purchase_cost', 'supplier_id',
];

    protected $casts = [
        'purchase_date' => 'date',
    ];

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }
    public function maintenances()
{
    return $this->hasMany(FacilityMaintenance::class);
}
}