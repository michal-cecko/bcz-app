<?php

namespace App\Policies;

use App\Models\Training;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class TrainingPolicy
{
    use HandlesAuthorization;

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
        if ($user->can('Update:Training')) {
            return true;
        }

        return $user->can('UpdateOwn:Training') && $training->created_by === $user->id;
    }

    public function delete(User $user, Training $training): bool
    {
        if ($user->can('Delete:Training')) {
            return true;
        }

        return $user->can('DeleteOwn:Training') && $training->created_by === $user->id;
    }

    public function restore(User $user, Training $training): bool
    {
        return $user->can('Restore:Training');
    }

    public function forceDelete(User $user, Training $training): bool
    {
        return $user->can('ForceDelete:Training');
    }

    public function forceDeleteAny(User $user): bool
    {
        return $user->can('ForceDeleteAny:Training');
    }

    public function restoreAny(User $user): bool
    {
        return $user->can('RestoreAny:Training');
    }

    public function replicate(User $user, Training $training): bool
    {
        return $user->can('Replicate:Training');
    }

    public function reorder(User $user): bool
    {
        return $user->can('Reorder:Training');
    }
}
