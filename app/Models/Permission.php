<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Spatie\Permission\Models\Permission as SpatiePermission;

class Permission extends SpatiePermission
{
    public function groups(): BelongsToMany
    {
        return $this->belongsToMany(UserGroup::class, 'group_permission', 'permission_id', 'group_id');
    }
}
