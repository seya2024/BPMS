<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Permission\Models\Permission;

class UserGroup extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'color',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'group_id');
    }

    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class, 'group_permission', 'group_id', 'permission_id');
    }

    public function hasPermission(string $permission): bool
    {
        return $this->permissions->contains('name', $permission);
    }

    public function assignPermission(string|array $permissions): void
    {
        $permissionIds = is_array($permissions)
            ? Permission::whereIn('name', $permissions)->pluck('id')
            : Permission::where('name', $permissions)->pluck('id');

        $this->permissions()->syncWithoutDetaching($permissionIds);
    }

    public function removePermission(string|array $permissions): void
    {
        $permissionIds = is_array($permissions)
            ? Permission::whereIn('name', $permissions)->pluck('id')
            : Permission::where('name', $permissions)->pluck('id');

        $this->permissions()->detach($permissionIds);
    }

    public function syncPermissions(array $permissions): void
    {
        $permissionIds = Permission::whereIn('name', $permissions)->pluck('id');
        $this->permissions()->sync($permissionIds);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
