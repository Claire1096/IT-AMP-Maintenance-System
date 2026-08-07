<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FinanceItemMonthlyLog extends Model
{
    protected $fillable = ['finance_item_id', 'month', 'quantity_on_hand', 'missing_quantity'];

    protected $casts = [
        'month' => 'date',
    ];

    public function item()
    {
        return $this->belongsTo(FinanceItem::class, 'finance_item_id');
    }
}
