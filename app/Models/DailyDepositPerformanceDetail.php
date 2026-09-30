<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DailyDepositPerformanceDetail extends Model
{
    protected $table = 'daily_deposit_performance_details';

    protected $fillable = [
        'branch_id',
        'business_day',
        'banking_type_id',
        'business_segment_id',
        'amount',
        'remarks',
    ];

    protected $casts = [
        'business_day' => 'date',
        'amount' => 'decimal:2',
    ];

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function bankingType()
    {
        return $this->belongsTo(BankingType::class, 'banking_type_id');
    }

    public function businessSegment()
    {
        return $this->belongsTo(BusinessSegment::class, 'business_segment_id');
    }
}