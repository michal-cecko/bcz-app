<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\ExerciseCategory;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;

class ExerciseCategoryPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:ExerciseCategory');
    }

    public function view(AuthUser $authUser, ExerciseCategory $exerciseCategory): bool
    {
        return $authUser->can('View:ExerciseCategory');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:ExerciseCategory');
    }

    public function update(AuthUser $authUser, ExerciseCategory $exerciseCategory): bool
    {
        return $authUser->can('Update:ExerciseCategory');
    }

    public function delete(AuthUser $authUser, ExerciseCategory $exerciseCategory): bool
    {
        return $authUser->can('Delete:ExerciseCategory');
    }

    public function restore(AuthUser $authUser, ExerciseCategory $exerciseCategory): bool
    {
        return $authUser->can('Restore:ExerciseCategory');
    }

    public function forceDelete(AuthUser $authUser, ExerciseCategory $exerciseCategory): bool
    {
        return $authUser->can('ForceDelete:ExerciseCategory');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:ExerciseCategory');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:ExerciseCategory');
    }

    public function replicate(AuthUser $authUser, ExerciseCategory $exerciseCategory): bool
    {
        return $authUser->can('Replicate:ExerciseCategory');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:ExerciseCategory');
    }
}
