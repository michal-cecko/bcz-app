<?php

namespace App\Policies;

use App\Enums\RoleEnum;
use App\Models\Membership;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class MembershipPolicy
{
    use HandlesAuthorization;

    protected function isGlobalAdmin(User $user): bool
    {
        return $user->hasRole([RoleEnum::SUPER_ADMIN, RoleEnum::ADMIN, RoleEnum::EDITOR]);
    }

    protected function isTeamAdminOf(User $user, ?string $teamId): bool
    {
        if (! $teamId) {
            return false;
        }

        return $user->teams()
            ->where('teams.id', $teamId)
            ->wherePivot('role', RoleEnum::TEAM_ADMIN->value)
            ->exists();
    }

    public function viewAny(User $user): bool
    {
        return $user->can('ViewAny:Membership');
    }

    public function view(User $user, Membership $membership): bool
    {
        if ($membership->user_id === $user->id) {
            return true;
        }

        if (! $user->can('View:Membership')) {
            return false;
        }

        return $this->isGlobalAdmin($user) || $this->isTeamAdminOf($user, $membership->team_id);
    }

    public function create(User $user): bool
    {
        return $user->can('Create:Membership');
    }

    public function update(User $user, Membership $membership): bool
    {
        if (! $user->can('Update:Membership')) {
            return false;
        }

        return $this->isGlobalAdmin($user) || $this->isTeamAdminOf($user, $membership->team_id);
    }

    public function delete(User $user, Membership $membership): bool
    {
        if (! $user->can('Delete:Membership')) {
            return false;
        }

        return $this->isGlobalAdmin($user) || $this->isTeamAdminOf($user, $membership->team_id);
    }

    public function restore(User $user, Membership $membership): bool
    {
        return $user->can('Restore:Membership') && $this->isGlobalAdmin($user);
    }

    public function forceDelete(User $user, Membership $membership): bool
    {
        return $user->can('ForceDelete:Membership') && $this->isGlobalAdmin($user);
    }
}
