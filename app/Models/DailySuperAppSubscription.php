<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DailySuperAppSubscription extends Model
{
    protected $table = 'daily_super_app_subscriptions';

    protected $fillable = [
        'branch_id',
        'business_day',
        'subscriptions',
        'target_subscriptions',
        'remarks',
    ];

    protected $casts = [
        'business_day' => 'date',
        'subscriptions' => 'integer',
        'target_subscriptions' => 'integer',
    ];

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }
}