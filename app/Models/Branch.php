<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Branch extends Model
{
 /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory;
    use Notifiable;
    use LogsActivity;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    // use SoftDeletes; // Enables soft delete functionality

    // protected $dates = ['deleted_at']; // optional in Laravel 12+

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll() // log all attributes
            ->logOnlyDirty() // optional, only log changed fields
            ->useLogName('Branch'); // optional, name of the log
    }

    protected $fillable = [
        'code',
        'name',
        'grade',
        'district_id',
        'created_at',
        'updated_at',
       // 'isClosed',
    ];

    public function district()
    {
        return $this->belongsTo(District::class, 'district_id');
    }

  public function bankingType()
    {
        return $this->belongsTo(BankingType::class, 'bankingType_id');
    }

    public function getFilamentName(): string
    {
        return "{$this->name}";
    }

    public function branchDepositPlans()
    {
        return $this->hasMany(BranchDepositPlan::class);
    }

    public function branchAccountPlans()
    {
        return $this->hasMany(BranchAccountPlan::class);
    }

    public function dailyAccountPerformances()
    {
        return $this->hasMany(DailyAccountPerformance::class);
    }

    public function dailyDepositPerformances()
    {
        return $this->hasMany(DailyDepositPerformance::class);
    }


}


/*

// Get all daily account performance records for a branch
$branch->dailyAccountPerformances;

// Get all daily deposit performance records for a branch
$branch->dailyDepositPerformances;

*/