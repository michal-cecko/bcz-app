<?php

namespace App\Policies;

use App\Models\Membership;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class MembershipPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('ViewAny:Membership');
    }

    public function view(User $user, Membership $membership): bool
    {
        return $user->can('View:Membership');
    }

    public function create(User $user): bool
    {
        return $user->can('Create:Membership');
    }

    public function update(User $user, Membership $membership): bool
    {
        return $user->can('Update:Membership');
    }

    public function delete(User $user, Membership $membership): bool
    {
        return $user->can('Delete:Membership');
    }

    public function restore(User $user, Membership $membership): bool
    {
        return $user->can('Restore:Membership');
    }

    public function forceDelete(User $user, Membership $membership): bool
    {
        return $user->can('ForceDelete:Membership');
    }
}
