<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DailyAccountOpening extends Model
{
    protected $fillable = [
        'branch_id',
        'business_day',
        'conventional_accounts',
        'ifb_accounts',
        'target_accounts',
        'remarks',
        'recorded_by',
    ];

    protected $casts = [
        'business_day' => 'date',
        'conventional_accounts' => 'integer',
        'ifb_accounts' => 'integer',
        'target_accounts' => 'integer',
    ];

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function recorder(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }

    public function getTotalAttribute(): int
    {
        return $this->conventional_accounts + $this->ifb_accounts;
    }

    public function getAchievementPercentAttribute(): float
    {
        if ($this->target_accounts <= 0) {
            return 0;
        }
        return round(($this->total / $this->target_accounts) * 100, 1);
    }

    public function getIsTargetMetAttribute(): bool
    {
        return $this->total >= $this->target_accounts;
    }
}
