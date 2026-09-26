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
        'supperappsubscription',
        'remarks',
        'created_by',
    ];

        protected $casts = [
            'deposit' => 'decimal:2',
            'account' => 'integer',
            'supperappsubscription' => 'integer',
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
}