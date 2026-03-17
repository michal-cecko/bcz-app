<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Exercise;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ExercisePolicy
{
    use HandlesAuthorization;

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
        if ($user->can('Update:Exercise')) {
            return true;
        }

        return $user->can('UpdateOwn:Exercise') && $exercise->created_by === $user->id;
    }

    public function delete(User $user, Exercise $exercise): bool
    {
        if ($user->can('Delete:Exercise')) {
            return true;
        }

        return $user->can('DeleteOwn:Exercise') && $exercise->created_by === $user->id;
    }

    public function restore(User $user, Exercise $exercise): bool
    {
        return $user->can('Restore:Exercise');
    }

    public function forceDelete(User $user, Exercise $exercise): bool
    {
        return $user->can('ForceDelete:Exercise');
    }

    public function forceDeleteAny(User $user): bool
    {
        return $user->can('ForceDeleteAny:Exercise');
    }

    public function restoreAny(User $user): bool
    {
        return $user->can('RestoreAny:Exercise');
    }

    public function replicate(User $user, Exercise $exercise): bool
    {
        return $user->can('Replicate:Exercise');
    }

    public function reorder(User $user): bool
    {
        return $user->can('Reorder:Exercise');
    }
}
