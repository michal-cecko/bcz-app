<?php

namespace App\Policies;

use App\Models\MediaItem;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class MediaItemPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('ViewAny:MediaItem');
    }

    public function view(User $user, MediaItem $mediaItem): bool
    {
        return $user->can('View:MediaItem');
    }

    public function create(User $user): bool
    {
        return $user->can('Create:MediaItem');
    }

    public function update(User $user, MediaItem $mediaItem): bool
    {
        if ($user->can('Update:MediaItem')) {
            return true;
        }

        return $user->can('UpdateOwn:MediaItem') && $mediaItem->created_by === $user->id;
    }

    public function delete(User $user, MediaItem $mediaItem): bool
    {
        if ($user->can('Delete:MediaItem')) {
            return true;
        }

        return $user->can('DeleteOwn:MediaItem') && $mediaItem->created_by === $user->id;
    }

    public function restore(User $user, MediaItem $mediaItem): bool
    {
        return $user->can('Restore:MediaItem');
    }

    public function forceDelete(User $user, MediaItem $mediaItem): bool
    {
        return $user->can('ForceDelete:MediaItem');
    }

    public function forceDeleteAny(User $user): bool
    {
        return $user->can('ForceDeleteAny:MediaItem');
    }

    public function restoreAny(User $user): bool
    {
        return $user->can('RestoreAny:MediaItem');
    }

    public function replicate(User $user, MediaItem $mediaItem): bool
    {
        return $user->can('Replicate:MediaItem');
    }

    public function reorder(User $user): bool
    {
        return $user->can('Reorder:MediaItem');
    }
}
