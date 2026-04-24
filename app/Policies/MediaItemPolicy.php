<?php

namespace App\Policies;

use App\Enums\RoleEnum;
use App\Models\MediaItem;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class MediaItemPolicy
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
        if (! $user->can('Update:MediaItem') && ! $user->can('UpdateOwn:MediaItem')) {
            return false;
        }

        return $this->isGlobalAdmin($user) || $this->isTeamAdminOf($user, $mediaItem->team_id);
    }

    public function delete(User $user, MediaItem $mediaItem): bool
    {
        if (! $user->can('Delete:MediaItem') && ! $user->can('DeleteOwn:MediaItem')) {
            return false;
        }

        return $this->isGlobalAdmin($user) || $this->isTeamAdminOf($user, $mediaItem->team_id);
    }

    public function restore(User $user, MediaItem $mediaItem): bool
    {
        return $user->can('Restore:MediaItem') && $this->isGlobalAdmin($user);
    }

    public function forceDelete(User $user, MediaItem $mediaItem): bool
    {
        return $user->can('ForceDelete:MediaItem') && $this->isGlobalAdmin($user);
    }

    public function forceDeleteAny(User $user): bool
    {
        return $user->can('ForceDeleteAny:MediaItem') && $this->isGlobalAdmin($user);
    }

    public function restoreAny(User $user): bool
    {
        return $user->can('RestoreAny:MediaItem') && $this->isGlobalAdmin($user);
    }

    public function replicate(User $user, MediaItem $mediaItem): bool
    {
        if (! $user->can('Replicate:MediaItem')) {
            return false;
        }

        return $this->isGlobalAdmin($user) || $this->isTeamAdminOf($user, $mediaItem->team_id);
    }

    public function reorder(User $user): bool
    {
        return $user->can('Reorder:MediaItem');
    }
}
