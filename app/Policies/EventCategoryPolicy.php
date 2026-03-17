<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\EventCategory;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;

class EventCategoryPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:EventCategory');
    }

    public function view(AuthUser $authUser, EventCategory $eventCategory): bool
    {
        return $authUser->can('View:EventCategory');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:EventCategory');
    }

    public function update(AuthUser $authUser, EventCategory $eventCategory): bool
    {
        return $authUser->can('Update:EventCategory');
    }

    public function delete(AuthUser $authUser, EventCategory $eventCategory): bool
    {
        return $authUser->can('Delete:EventCategory');
    }

    public function restore(AuthUser $authUser, EventCategory $eventCategory): bool
    {
        return $authUser->can('Restore:EventCategory');
    }

    public function forceDelete(AuthUser $authUser, EventCategory $eventCategory): bool
    {
        return $authUser->can('ForceDelete:EventCategory');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:EventCategory');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:EventCategory');
    }

    public function replicate(AuthUser $authUser, EventCategory $eventCategory): bool
    {
        return $authUser->can('Replicate:EventCategory');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:EventCategory');
    }
}
