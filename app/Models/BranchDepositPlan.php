<?php

namespace App\Models;

use App\Models\AnnualDepositPlan;
use Illuminate\Database\Eloquent\Model;

class BranchDepositPlan extends Model
{

    protected $fillable = [
        'annual_deposit_plan_id',
        'branch_id',

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

    public function annualDepositPlan()
    {
        return $this->belongsTo(AnnualDepositPlan::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }
}