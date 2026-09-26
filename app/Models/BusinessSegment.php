<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BusinessSegment extends Model
{
    use HasFactory;
    protected $table = 'business_segments';
    protected $fillable = [
        'name',
        'description',
    ];
    /*
    |-----------------------------------------------------
    | Casts (optional but good practice)
    |-----------------------------------------------------
    */
    protected $casts = [
        'name' => 'string',
        'description' => 'string',
    ];
    /*
    |-----------------------------------------------------
    | Relationships (future-ready)
    |-----------------------------------------------------
    */
    public function branches()
    {
        return $this->hasMany(Branch::class, 'business_segment_id');
    }
    /*
    |-----------------------------------------------------
    | Scopes
    |-----------------------------------------------------
    */
    public function scopeHasName($query)
    {
        return $query->whereNotNull('name');
    }
}