<?php

namespace App\Policies;

use App\Models\TeamSubscription;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class TeamSubscriptionPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('ViewAny:TeamSubscription');
    }

    public function view(User $user, TeamSubscription $subscription): bool
    {
        return $user->can('View:TeamSubscription');
    }

    public function create(User $user): bool
    {
        return $user->can('Create:TeamSubscription');
    }

    public function update(User $user, TeamSubscription $subscription): bool
    {
        return $user->can('Update:TeamSubscription');
    }

    public function delete(User $user, TeamSubscription $subscription): bool
    {
        return $user->can('Delete:TeamSubscription');
    }

    public function restore(User $user, TeamSubscription $subscription): bool
    {
        return $user->can('Restore:TeamSubscription');
    }

    public function forceDelete(User $user, TeamSubscription $subscription): bool
    {
        return $user->can('ForceDelete:TeamSubscription');
    }
}
