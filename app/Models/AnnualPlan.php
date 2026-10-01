<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AnnualPlan extends Model
{
    protected $fillable = [
        'financial_year_id',
        'district_id',
        'deposit',
        'account',
        'super_app_subscriptions',
        'foreign_currency_target',
        'remarks',
        'created_by',
        'approval_status',
        'approved_by',
        'approved_at',
        'approval_remarks',
    ];

    protected $casts = [
        'deposit' => 'decimal:2',
        'account' => 'integer',
        'super_app_subscriptions' => 'integer',
        'foreign_currency_target' => 'decimal:2',
        'approved_at' => 'datetime',
    ];

    public function financialYear()
    {
        return $this->belongsTo(FinancialYear::class);
    }

    public function district()
    {
        return $this->belongsTo(District::class);
    }

    public function depositPlans(): HasMany
    {
        return $this->hasMany(AnnualDepositPlan::class);
    }

    public function accountPlans(): HasMany
    {
        return $this->hasMany(AnnualAccountPlan::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
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
