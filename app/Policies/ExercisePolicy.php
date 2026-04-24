<?php

declare(strict_types=1);

namespace App\Policies;

use App\Enums\RoleEnum;
use App\Models\Exercise;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ExercisePolicy
{
    use HandlesAuthorization;

    protected function isGlobalAdmin(User $user): bool
    {
        return $user->hasRole([RoleEnum::SUPER_ADMIN, RoleEnum::ADMIN, RoleEnum::EDITOR]);
    }

    protected function isCoachOrTeamAdminOf(User $user, ?string $teamId): bool
    {
        if (! $teamId) {
            return false;
        }

        return $user->teams()
            ->where('teams.id', $teamId)
            ->wherePivotIn('role', [RoleEnum::TEAM_ADMIN->value, RoleEnum::COACH->value])
            ->exists();
    }

    public function viewAny(User $user): bool
    {
        return $user->can('ViewAny:Exercise');
    }

    public function view(User $user, Exercise $exercise): bool
    {
        return $user->can('View:Exercise');
    }

    public function create(User $user): bool
    {
        return $user->can('Create:Exercise');
    }

    public function update(User $user, Exercise $exercise): bool
    {
        if (! $user->can('Update:Exercise') && ! $user->can('UpdateOwn:Exercise')) {
            return false;
        }

        return $this->isGlobalAdmin($user) || $this->isCoachOrTeamAdminOf($user, $exercise->team_id);
    }

    public function delete(User $user, Exercise $exercise): bool
    {
        if (! $user->can('Delete:Exercise') && ! $user->can('DeleteOwn:Exercise')) {
            return false;
        }

        return $this->isGlobalAdmin($user) || $this->isCoachOrTeamAdminOf($user, $exercise->team_id);
    }

    public function restore(User $user, Exercise $exercise): bool
    {
        return $user->can('Restore:Exercise') && $this->isGlobalAdmin($user);
    }

    public function forceDelete(User $user, Exercise $exercise): bool
    {
        return $user->can('ForceDelete:Exercise') && $this->isGlobalAdmin($user);
    }

    public function forceDeleteAny(User $user): bool
    {
        return $user->can('ForceDeleteAny:Exercise') && $this->isGlobalAdmin($user);
    }

    public function restoreAny(User $user): bool
    {
        return $user->can('RestoreAny:Exercise') && $this->isGlobalAdmin($user);
    }

    public function replicate(User $user, Exercise $exercise): bool
    {
        if (! $user->can('Replicate:Exercise')) {
            return false;
        }

        return $this->isGlobalAdmin($user) || $this->isCoachOrTeamAdminOf($user, $exercise->team_id);
    }

    public function reorder(User $user): bool
    {
        return $user->can('Reorder:Exercise');
    }
}
