<?php

namespace App\Policies;

use App\Models\AnnualPlan;
use App\Models\User;

class AnnualPlanPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('view annual plans');
    }

    public function view(User $user, AnnualPlan $annualPlan): bool
    {
        return $user->hasPermissionTo('view annual plans');
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('create annual plans');
    }

    public function update(User $user, AnnualPlan $annualPlan): bool
    {
        return $user->hasPermissionTo('edit annual plans');
    }

    public function delete(User $user, AnnualPlan $annualPlan): bool
    {
        return $user->hasPermissionTo('delete annual plans');
    }

    public function submit(User $user, AnnualPlan $annualPlan): bool
    {
        return $user->hasPermissionTo('submit annual plans');
    }

    public function approve(User $user, AnnualPlan $annualPlan): bool
    {
        return $user->hasPermissionTo('approve annual plans');
    }

    public function reject(User $user, AnnualPlan $annualPlan): bool
    {
        return $user->hasPermissionTo('reject annual plans');
    }
}
