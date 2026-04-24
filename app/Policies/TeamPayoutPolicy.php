<?php

namespace App\Policies;

use App\Enums\RoleEnum;
use App\Models\TeamPayout;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class TeamPayoutPolicy
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
        return $user->can('ViewAny:TeamPayout');
    }

    public function view(User $user, TeamPayout $payout): bool
    {
        if (! $user->can('View:TeamPayout')) {
            return false;
        }

        return $this->isGlobalAdmin($user) || $this->isTeamAdminOf($user, $payout->team_id);
    }

    public function create(User $user): bool
    {
        return $user->can('Create:TeamPayout') && $this->isGlobalAdmin($user);
    }

    public function update(User $user, TeamPayout $payout): bool
    {
        return $user->can('Update:TeamPayout') && $this->isGlobalAdmin($user);
    }

    public function delete(User $user, TeamPayout $payout): bool
    {
        return $user->can('Delete:TeamPayout') && $this->isGlobalAdmin($user);
    }

    public function restore(User $user, TeamPayout $payout): bool
    {
        return $user->can('Restore:TeamPayout') && $this->isGlobalAdmin($user);
    }

    public function forceDelete(User $user, TeamPayout $payout): bool
    {
        return $user->can('ForceDelete:TeamPayout') && $this->isGlobalAdmin($user);
    }
}
