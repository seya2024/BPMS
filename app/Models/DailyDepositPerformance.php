<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DailyDepositPerformance extends Model
{

    protected $fillable = [
        'branch_id',
        'business_day',
        'total_deposit_amount',
        'new_deposit_amount',
        'deposit_inflow_amount',
        'deposit_outflow_amount',
        'net_deposit_change',
        'remarks',
    ];

    protected $casts = [
        'business_day' => 'date',
        'total_deposit_amount' => 'decimal:2',
        'new_deposit_amount' => 'decimal:2',
        'deposit_inflow_amount' => 'decimal:2',
        'deposit_outflow_amount' => 'decimal:2',
        'net_deposit_change' => 'decimal:2',
    ];

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function details()
    {
        return $this->hasMany(DailyDepositPerformanceDetail::class, 'branch_id', 'branch_id')
            ->whereColumn('business_day', 'business_day');
    }
}