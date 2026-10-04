<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class BankingType extends Model
{
    //
    
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
            ->useLogName('BankingType'); // optional, name of the log
    }


    protected $fillable = [
        'name',
        'created_at',
        'updated_at',
    ];
    

    public function branches()
{
    return $this->hasMany(Branch::class, 'bankingType_id');
}

public function ifbBranches()
{
    return $this->hasMany(Branch::class, 'bankingType_id')
        ->where('name', 'like', '%IFB%');
}
}
