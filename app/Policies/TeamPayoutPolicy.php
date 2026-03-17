<?php

namespace App\Policies;

use App\Models\TeamPayout;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class TeamPayoutPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('ViewAny:TeamPayout');
    }

    public function view(User $user, TeamPayout $payout): bool
    {
        return $user->can('View:TeamPayout');
    }

    public function create(User $user): bool
    {
        return $user->can('Create:TeamPayout');
    }

    public function update(User $user, TeamPayout $payout): bool
    {
        return $user->can('Update:TeamPayout');
    }

    public function delete(User $user, TeamPayout $payout): bool
    {
        return $user->can('Delete:TeamPayout');
    }

    public function restore(User $user, TeamPayout $payout): bool
    {
        return $user->can('Restore:TeamPayout');
    }

    public function forceDelete(User $user, TeamPayout $payout): bool
    {
        return $user->can('ForceDelete:TeamPayout');
    }
}
