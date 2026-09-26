<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class KPICategory extends Model
{
    //

    use HasFactory;

    protected $table = 'k_p_i_categories';

    protected $fillable = [
        'name',
        'description',
        // 'weight',
        // 'is_active',
    ];

//     public function kpis()
// {
//     return $this->hasMany(KPI::class);
// }



    /*
    |-----------------------------------------------------
    | Relationships
    |-----------------------------------------------------
    */

    public function kpis()
    {
        return $this->hasMany(KPI::class, 'category_id');
    }

    /*
    |-----------------------------------------------------
    | Scopes
    |-----------------------------------------------------
    */

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /*
    |-----------------------------------------------------
    | Helper Methods
    |-----------------------------------------------------
    */

    public function isActive(): bool
    {
        return (bool) $this->is_active;
    }
    
}
