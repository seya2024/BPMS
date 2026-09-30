<?php

namespace App\Policies;

use App\Models\BranchAccountPlan;
use App\Models\BranchDepositPlan;
use App\Models\User;

class BranchPlanPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('view branch plans');
    }

    public function view(User $user, BranchDepositPlan|BranchAccountPlan $plan): bool
    {
        return $user->hasPermissionTo('view branch plans');
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('create branch plans');
    }

    public function update(User $user, BranchDepositPlan|BranchAccountPlan $plan): bool
    {
        return $user->hasPermissionTo('edit branch plans');
    }

    public function delete(User $user, BranchDepositPlan|BranchAccountPlan $plan): bool
    {
        return $user->hasPermissionTo('delete branch plans');
    }

    public function submit(User $user, BranchDepositPlan|BranchAccountPlan $plan): bool
    {
        return $user->hasPermissionTo('submit branch plans');
    }

    public function approve(User $user, BranchDepositPlan|BranchAccountPlan $plan): bool
    {
        return $user->hasPermissionTo('approve branch plans');
    }

    public function reject(User $user, BranchDepositPlan|BranchAccountPlan $plan): bool
    {
        return $user->hasPermissionTo('reject branch plans');
    }
}
