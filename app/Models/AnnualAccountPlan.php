<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AnnualAccountPlan extends Model
{
      protected $fillable = [
        'annual_plan_id',
        'annual_target_accounts',
        'q1_target_accounts',
        'q2_target_accounts',
        'q3_target_accounts',
        'q4_target_accounts',
        'monthly_target_accounts',
        'weekly_target_accounts',
        'daily_target_accounts',
        'remarks',
    ];

    protected $casts = [
    'annual_target_accounts' => 'integer',
    'q1_target_accounts' => 'integer',
    'q2_target_accounts' => 'integer',
    'q3_target_accounts' => 'integer',
    'q4_target_accounts' => 'integer',

    'monthly_target_accounts' => 'decimal:2',
    'weekly_target_accounts' => 'decimal:2',
    'daily_target_accounts' => 'decimal:2',
];


     public function annualPlan(): BelongsTo
    {
        return $this->belongsTo(AnnualPlan::class);
    }
    
    public function branchAccountPlans()
{
    return $this->hasMany(BranchAccountPlan::class);
}
}
