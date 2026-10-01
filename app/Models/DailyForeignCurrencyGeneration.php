<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DailyForeignCurrencyGeneration extends Model
{
    protected $table = 'daily_foreign_currency_generations';

    protected $fillable = [
        'branch_id',
        'business_day',
        'amount',
        'target_amount',
        'currency_code',
        'remarks',
    ];

    protected $casts = [
        'business_day' => 'date',
        'amount' => 'decimal:2',
        'target_amount' => 'decimal:2',
    ];

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }
}