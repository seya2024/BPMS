<?php

namespace App\Models;

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
        'approval_status',
        'approved_by',
        'approved_at',
        'approval_remarks',
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
        'approved_at' => 'datetime',
    ];

    public function annualDepositPlan()
    {
        return $this->belongsTo(AnnualDepositPlan::class);
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
