<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FinanceMonthlyCountItem extends Model
{
    protected $fillable = [
        'finance_monthly_count_id', 'finance_item_id', 'expected_quantity',
        'counted_quantity', 'department_id', 'checked_at',
    ];

    protected $casts = [
        'checked_at' => 'datetime',
    ];

    public function count()
    {
        return $this->belongsTo(FinanceMonthlyCount::class, 'finance_monthly_count_id');
    }

    public function financeItem()
    {
        return $this->belongsTo(FinanceItem::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }
}
