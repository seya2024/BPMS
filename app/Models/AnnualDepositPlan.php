<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AnnualDepositPlan extends Model
{
    //
 protected $fillable = [
        'annual_plan_id',
        'annual_target_amount',
        'q1_target_amount',
        'q2_target_amount',
        'q3_target_amount',
        'q4_target_amount',
        'monthly_target_amount',
        'weekly_target_amount',
        'daily_target_amount',
        'remarks',
    ];

    protected $casts = [
    'annual_target_amount' => 'decimal:2',
    'q1_target_amount' => 'decimal:2',
    'q2_target_amount' => 'decimal:2',
    'q3_target_amount' => 'decimal:2',
    'q4_target_amount' => 'decimal:2',
    'monthly_target_amount' => 'decimal:2',
    'weekly_target_amount' => 'decimal:2',
    'daily_target_amount' => 'decimal:2',
];

       public function annualPlan(): BelongsTo
    {
        return $this->belongsTo(AnnualPlan::class);
    }

    public function branchDepositPlans()
{
    return $this->hasMany(BranchDepositPlan::class);
}
}
