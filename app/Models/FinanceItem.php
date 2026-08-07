<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FinanceItem extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'item_tag', 'name', 'asset_type', 'quantity', 'current_quantity',
        'missing_quantity', 'department_id', 'status', 'missing_since',
    ];

    protected $casts = [
        'missing_since' => 'datetime',
    ];

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function monthlyLogs()
    {
        return $this->hasMany(FinanceItemMonthlyLog::class);
    }
}
