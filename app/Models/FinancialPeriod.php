<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FinancialPeriod extends Model
{
    use HasFactory;

    protected $table = 'financial_periods';

    protected $fillable = [
        'financial_year_id',
        'quarter',
        'label',
        'start_date',
        'end_date',
        'status',
        'closed_at',
    ];

    /*
    |-----------------------------------------------------
    | Casts
    |-----------------------------------------------------
    */

    protected $casts = [
        'start_date' => 'date',
        'end_date'   => 'date',
        'closed_at'  => 'datetime',
        'quarter'    => 'integer',
    ];

    /*
    |-----------------------------------------------------
    | Relationships
    |-----------------------------------------------------
    */

    public function financialYear()
    {
        return $this->belongsTo(FinancialYear::class);
    }

    /*
    |-----------------------------------------------------
    | Scopes
    |-----------------------------------------------------
    */

    public function scopeOpen($query)
    {
        return $query->where('status', 'OPEN');
    }

    public function scopeClosed($query)
    {
        return $query->where('status', 'CLOSED');
    }

    public function scopeQuarter($query, int $quarter)
    {
        return $query->where('quarter', $quarter);
    }

    /*
    |-----------------------------------------------------
    | Helpers (banking logic)
    |-----------------------------------------------------
    */

    public function isOpen(): bool
    {
        return $this->status === 'OPEN';
    }

    public function isClosed(): bool
    {
        return $this->status === 'CLOSED';
    }

    public function isQ1(): bool
    {
        return $this->quarter === 1;
    }

    public function isQ2(): bool
    {
        return $this->quarter === 2;
    }

    public function isQ3(): bool
    {
        return $this->quarter === 3;
    }

    public function isQ4(): bool
    {
        return $this->quarter === 4;
    }
}