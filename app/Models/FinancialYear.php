<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FinancialYear extends Model
{
    use HasFactory;

    protected $table = 'financial_years';

    protected $fillable = [
        'name',
        'start_date',
        'end_date',
        'status',
        'opened_at',
        'closed_at',
        'closed_by',
    ];

    /*
    |-----------------------------------------------------
    | Casts
    |-----------------------------------------------------
    */

    protected $casts = [
        'start_date' => 'date',
        'end_date'   => 'date',
        'opened_at'  => 'datetime',
        'closed_at'  => 'datetime',
    ];

    /*
    |-----------------------------------------------------
    | Relationships
    |-----------------------------------------------------
    */

    public function closedBy()
    {
        return $this->belongsTo(User::class, 'closed_by');
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

    public function scopeClosing($query)
    {
        return $query->where('status', 'CLOSING');
    }

    public function periods()
    {
        return $this->hasMany(FinancialPeriod::class);
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

    public function isClosing(): bool
    {
        return $this->status === 'CLOSING';
    }

    /*
    |-----------------------------------------------------
    | Business Logic Helper
    |-----------------------------------------------------
    */

    public static function current()
    {
        return self::where('status', 'OPEN')->firstOrFail();
    }
}