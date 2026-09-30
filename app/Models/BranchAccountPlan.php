<?php

namespace App\Models;

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
        'approval_status',
        'approved_by',
        'approved_at',
        'approval_remarks',
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
        'approved_at' => 'datetime',
    ];

    public function annualAccountPlan()
    {
        return $this->belongsTo(AnnualAccountPlan::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    // Approval status helpers
    public function isDraft(): bool
    {
        return $this->approval_status === 'draft';
    }

    public function isPending(): bool
    {
        return $this->approval_status === 'pending';
    }

    public function isApproved(): bool
    {
        return $this->approval_status === 'approved';
    }

    public function isRejected(): bool
    {
        return $this->approval_status === 'rejected';
    }

    public function canBeSubmitted(): bool
    {
        return $this->isDraft() || $this->isRejected();
    }

    public function canBeApproved(): bool
    {
        return $this->isPending();
    }

    // Scopes
    public function scopeDraft($query)
    {
        return $query->where('approval_status', 'draft');
    }

    public function scopePending($query)
    {
        return $query->where('approval_status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('approval_status', 'approved');
    }

    public function scopeRejected($query)
    {
        return $query->where('approval_status', 'rejected');
    }
}
