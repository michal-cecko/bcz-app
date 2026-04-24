<?php

declare(strict_types=1);

namespace App\Policies;

use App\Enums\RoleEnum;
use App\Models\ExerciseCategory;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ExerciseCategoryPolicy
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
        return $user->can('ViewAny:ExerciseCategory');
    }

    public function view(User $user, ExerciseCategory $exerciseCategory): bool
    {
        return $user->can('View:ExerciseCategory');
    }

    public function create(User $user): bool
    {
        return $user->can('Create:ExerciseCategory');
    }

    public function update(User $user, ExerciseCategory $exerciseCategory): bool
    {
        if (! $user->can('Update:ExerciseCategory')) {
            return false;
        }

        return $this->isGlobalAdmin($user) || $this->isCoachOrTeamAdminOf($user, $exerciseCategory->team_id);
    }

    public function delete(User $user, ExerciseCategory $exerciseCategory): bool
    {
        if (! $user->can('Delete:ExerciseCategory')) {
            return false;
        }

        return $this->isGlobalAdmin($user) || $this->isCoachOrTeamAdminOf($user, $exerciseCategory->team_id);
    }

    public function restore(User $user, ExerciseCategory $exerciseCategory): bool
    {
        return $user->can('Restore:ExerciseCategory') && $this->isGlobalAdmin($user);
    }

    public function forceDelete(User $user, ExerciseCategory $exerciseCategory): bool
    {
        return $user->can('ForceDelete:ExerciseCategory') && $this->isGlobalAdmin($user);
    }

    public function forceDeleteAny(User $user): bool
    {
        return $user->can('ForceDeleteAny:ExerciseCategory') && $this->isGlobalAdmin($user);
    }

    public function restoreAny(User $user): bool
    {
        return $user->can('RestoreAny:ExerciseCategory') && $this->isGlobalAdmin($user);
    }

    public function replicate(User $user, ExerciseCategory $exerciseCategory): bool
    {
        if (! $user->can('Replicate:ExerciseCategory')) {
            return false;
        }

        return $this->isGlobalAdmin($user) || $this->isCoachOrTeamAdminOf($user, $exerciseCategory->team_id);
    }

    public function reorder(User $user): bool
    {
        return $user->can('Reorder:ExerciseCategory');
    }
}
