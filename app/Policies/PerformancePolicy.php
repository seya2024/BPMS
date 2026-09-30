<?php

namespace App\Policies;

use App\Models\DailyAccountPerformance;
use App\Models\DailyDepositPerformance;
use App\Models\User;

class PerformancePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('view performances');
    }

    public function view(User $user, DailyDepositPerformance|DailyAccountPerformance $performance): bool
    {
        return $user->hasPermissionTo('view performances');
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('create performances');
    }

    public function update(User $user, DailyDepositPerformance|DailyAccountPerformance $performance): bool
    {
        return $user->hasPermissionTo('edit performances');
    }

    public function delete(User $user, DailyDepositPerformance|DailyAccountPerformance $performance): bool
    {
        return $user->hasPermissionTo('delete performances');
    }
}
