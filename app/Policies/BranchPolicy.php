<?php

namespace App\Policies;

use App\Models\Branch;
use App\Models\User;

class BranchPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('view branches');
    }

    public function view(User $user, Branch $branch): bool
    {
        return $user->hasPermissionTo('view branches');
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('create branches');
    }

    public function update(User $user, Branch $branch): bool
    {
        return $user->hasPermissionTo('edit branches');
    }

    public function delete(User $user, Branch $branch): bool
    {
        return $user->hasPermissionTo('delete branches');
    }
}
