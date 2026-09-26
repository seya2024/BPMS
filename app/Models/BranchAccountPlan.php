<?php

namespace App\Models;

use App\Models\AnnualAccountPlan;
use Illuminate\Database\Eloquent\Model;

class BranchAccountPlan extends Model
{
   
    protected $fillable = [
        'annual_account_plan_id',
        'branch_id',

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
        'monthly_target_accounts' => 'integer',
        'weekly_target_accounts' => 'integer',
        'daily_target_accounts' => 'integer',
    ];

    public function annualAccountPlan()
    {
        return $this->belongsTo(AnnualAccountPlan::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }
}