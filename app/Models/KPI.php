<?php

namespace App\Models;

use App\Models\KPICategory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KPI extends Model
{
    //

     use HasFactory;

    protected $table = 'k_p_i_s';

    protected $fillable = [
        'name',
        'category_id',
        'unit',
        'calculation_method',
   
    ];

     protected $casts = [
        'report_date' => 'date',
        'deposit' => 'decimal:2',
        'fcy' => 'decimal:2',
        'service_quality' => 'decimal:2',
    ];

  public function category()
    {
        return $this->belongsTo(KPICategory::class, 'category_id');
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
}
