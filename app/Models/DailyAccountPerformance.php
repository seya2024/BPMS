<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DailyAccountPerformance extends Model
{
    protected $fillable = [
        'branch_id',
        'business_day',
        'total_accounts',
        'active_accounts',
        'new_accounts',
        'dormant_accounts',
        'reactivated_accounts',
        'remarks',
    ];

    protected $casts = [
        'business_day' => 'date',
        'total_accounts' => 'integer',
        'active_accounts' => 'integer',
        'new_accounts' => 'integer',
        'dormant_accounts' => 'integer',
        'reactivated_accounts' => 'integer',
    ];

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }
}