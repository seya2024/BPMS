<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, HasApiTokens, SoftDeletes;
    use HasRoles {
        hasPermissionTo as traitHasPermissionTo;
    }

    protected $fillable = [
        'name',
        'email',
        'password',
        'group_id',
        'status',
        'is_active',
        'is_locked',
        'must_change_password',
        'last_login_at',
        'last_login_ip',
        'failed_login_attempts',
        'branch_id',
        'district_id',
        'department',
        'job_title',
        'phone',
        'mfa_enabled',
        'mfa_secret',
        'approved_by',
        'approved_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'mfa_secret',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
            'is_locked' => 'boolean',
            'must_change_password' => 'boolean',
            'mfa_enabled' => 'boolean',
            'last_login_at' => 'datetime',
            'locked_at' => 'datetime',
            'password_changed_at' => 'datetime',
            'approved_at' => 'datetime',
            'failed_login_attempts' => 'integer',
        ];
    }

    // Relationships
    public function group(): BelongsTo
    {
        return $this->belongsTo(UserGroup::class, 'group_id');
    }

    public function groups(): BelongsToMany
    {
        return $this->belongsToMany(UserGroup::class, 'user_group_user', 'user_id', 'group_id');
    }

    public function branches(): BelongsToMany
    {
        return $this->belongsToMany(Branch::class, 'user_branch')->withPivot('is_primary');
    }

    public function primaryBranch(): BelongsTo
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

    public function district(): BelongsTo
    {
        return $this->belongsTo(District::class, 'district_id');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function loginHistories(): HasMany
    {
        return $this->hasMany(LoginHistory::class);
    }

    public function audits(): HasMany
    {
        return $this->hasMany(UserAudit::class);
    }

    public function closedFinancialYears(): HasMany
    {
        return $this->hasMany(FinancialYear::class, 'closed_by');
    }

    public function annualPlans(): HasMany
    {
        return $this->hasMany(AnnualPlan::class, 'created_by');
    }

    public function approvedPlans(): HasMany
    {
        return $this->hasMany(AnnualPlan::class, 'approved_by');
    }

    // Permission helpers
    public function hasGroupPermission(string $permission): bool
    {
        // Check direct group
        if ($this->group && $this->group->hasPermission($permission)) {
            return true;
        }

        // Check additional groups
        foreach ($this->groups as $group) {
            if ($group->hasPermission($permission)) {
                return true;
            }
        }

        return false;
    }

    public function getGroupPermissions(): array
    {
        $permissions = [];

        if ($this->group) {
            $permissions = array_merge($permissions, $this->group->permissions->pluck('name')->toArray());
        }

        foreach ($this->groups as $group) {
            $permissions = array_merge($permissions, $group->permissions->pluck('name')->toArray());
        }

        return array_unique($permissions);
    }

    public function hasPermissionTo($permission, $guardName = null): bool
    {
        if ($this->traitHasPermissionTo($permission, $guardName)) {
            return true;
        }

        return $this->hasGroupPermission($permission);
    }

    // Status helpers
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Panel access is granted to active, unlocked users.
     *
     * Required by Filament: without this the panel is only reachable when
     * app.env is "local", which breaks in every other environment.
     */
    public function canAccessPanel(Panel $panel): bool
    {
        if (! $this->is_active || $this->is_locked) {
            return false;
        }

        if ($panel->getId() === 'admin') {
            return true;
        }

        return $this->canLogin();
    }

    public function isInactive(): bool
    {
        return $this->status === 'inactive';
    }

    public function isLocked(): bool
    {
        return $this->is_locked || $this->status === 'locked';
    }

    public function canLogin(): bool
    {
        return $this->is_active && !$this->is_locked && $this->status === 'active';
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true)->where('status', 'active');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeLocked($query)
    {
        return $query->where('is_locked', true)->orWhere('status', 'locked');
    }

    public function scopeInactive($query)
    {
        return $query->where('is_active', false)->orWhere('status', 'inactive');
    }
}
