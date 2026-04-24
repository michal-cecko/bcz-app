<?php

declare(strict_types=1);

namespace App\Policies;

use App\Enums\RoleEnum;
use App\Models\Team;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;

class TeamPolicy
{
    use HandlesAuthorization;

    protected function isGlobalAdmin(AuthUser $user): bool
    {
        return $user->hasRole([RoleEnum::SUPER_ADMIN, RoleEnum::ADMIN, RoleEnum::EDITOR]);
    }

    protected function isTeamAdminOf(AuthUser $user, Team $team): bool
    {
        return $user->teams()
            ->where('teams.id', $team->id)
            ->wherePivot('role', RoleEnum::TEAM_ADMIN->value)
            ->exists();
    }

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Team');
    }

    public function view(AuthUser $authUser, Team $team): bool
    {
        if (! $authUser->can('View:Team')) {
            return false;
        }

        return $this->isGlobalAdmin($authUser) || $this->isTeamAdminOf($authUser, $team);
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Team');
    }

    public function update(AuthUser $authUser, Team $team): bool
    {
        if (! $authUser->can('Update:Team')) {
            return false;
        }

        return $this->isGlobalAdmin($authUser) || $this->isTeamAdminOf($authUser, $team);
    }

    public function delete(AuthUser $authUser, Team $team): bool
    {
        if (! $authUser->can('Delete:Team')) {
            return false;
        }

        return $this->isGlobalAdmin($authUser) || $this->isTeamAdminOf($authUser, $team);
    }

    public function restore(AuthUser $authUser, Team $team): bool
    {
        if (! $authUser->can('Restore:Team')) {
            return false;
        }

        return $this->isGlobalAdmin($authUser) || $this->isTeamAdminOf($authUser, $team);
    }

    public function forceDelete(AuthUser $authUser, Team $team): bool
    {
        return $authUser->can('ForceDelete:Team') && $this->isGlobalAdmin($authUser);
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:Team') && $this->isGlobalAdmin($authUser);
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:Team') && $this->isGlobalAdmin($authUser);
    }

    public function replicate(AuthUser $authUser, Team $team): bool
    {
        if (! $authUser->can('Replicate:Team')) {
            return false;
        }

        return $this->isGlobalAdmin($authUser) || $this->isTeamAdminOf($authUser, $team);
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:Team');
    }
}
