<?php

namespace App\Policies;

use App\Models\SubscriptionPlan;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class SubscriptionPlanPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('ViewAny:SubscriptionPlan');
    }

    public function view(User $user, SubscriptionPlan $plan): bool
    {
        return $user->can('View:SubscriptionPlan');
    }

    public function create(User $user): bool
    {
        return $user->can('Create:SubscriptionPlan');
    }

    public function update(User $user, SubscriptionPlan $plan): bool
    {
        return $user->can('Update:SubscriptionPlan');
    }

    public function delete(User $user, SubscriptionPlan $plan): bool
    {
        return $user->can('Delete:SubscriptionPlan');
    }

    public function restore(User $user, SubscriptionPlan $plan): bool
    {
        return $user->can('Restore:SubscriptionPlan');
    }

    public function forceDelete(User $user, SubscriptionPlan $plan): bool
    {
        return $user->can('ForceDelete:SubscriptionPlan');
    }
}
