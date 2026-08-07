<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FinanceMonthlyCount extends Model
{
    protected $fillable = ['month', 'status', 'created_by', 'closed_at'];

    protected $casts = [
        'month' => 'date',
        'closed_at' => 'datetime',
    ];

    public function items()
    {
        return $this->hasMany(FinanceMonthlyCountItem::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
