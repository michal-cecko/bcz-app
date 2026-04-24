<?php

namespace App\Policies;

use App\Enums\RoleEnum;
use App\Models\Training;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class TrainingPolicy
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

    protected function isAssignedCoach(User $user, Training $training): bool
    {
        return $training->coaches()->where('users.id', $user->id)->exists();
    }

    public function viewAny(User $user): bool
    {
        return $user->can('ViewAny:Training');
    }

    public function view(User $user, Training $training): bool
    {
        return $user->can('View:Training');
    }

    public function create(User $user): bool
    {
        return $user->can('Create:Training');
    }

    public function update(User $user, Training $training): bool
    {
        if (! $user->can('Update:Training') && ! $user->can('UpdateOwn:Training')) {
            return false;
        }

        if ($this->isGlobalAdmin($user)) {
            return true;
        }

        if ($this->isTeamAdminOf($user, $training->team_id)) {
            return true;
        }

        return $this->isAssignedCoach($user, $training);
    }

    public function delete(User $user, Training $training): bool
    {
        if (! $user->can('Delete:Training') && ! $user->can('DeleteOwn:Training')) {
            return false;
        }

        if ($this->isGlobalAdmin($user)) {
            return true;
        }

        if ($this->isTeamAdminOf($user, $training->team_id)) {
            return true;
        }

        return $this->isAssignedCoach($user, $training);
    }

    public function restore(User $user, Training $training): bool
    {
        return $user->can('Restore:Training') && $this->isGlobalAdmin($user);
    }

    public function forceDelete(User $user, Training $training): bool
    {
        return $user->can('ForceDelete:Training') && $this->isGlobalAdmin($user);
    }

    public function forceDeleteAny(User $user): bool
    {
        return $user->can('ForceDeleteAny:Training') && $this->isGlobalAdmin($user);
    }

    public function restoreAny(User $user): bool
    {
        return $user->can('RestoreAny:Training') && $this->isGlobalAdmin($user);
    }

    public function replicate(User $user, Training $training): bool
    {
        if (! $user->can('Replicate:Training')) {
            return false;
        }

        return $this->isGlobalAdmin($user) || $this->isTeamAdminOf($user, $training->team_id);
    }

    public function reorder(User $user): bool
    {
        return $user->can('Reorder:Training');
    }
}
