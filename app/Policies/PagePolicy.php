<?php

namespace App\Policies;

use App\Models\Page;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class PagePolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('ViewAny:Page');
    }

    public function view(User $user, Page $page): bool
    {
        return $user->can('View:Page');
    }

    public function create(User $user): bool
    {
        return $user->can('Create:Page');
    }

    public function update(User $user, Page $page): bool
    {
        return $user->can('Update:Page');
    }

    public function delete(User $user, Page $page): bool
    {
        if ($page->is_system) {
            return false;
        }

        return $user->can('Delete:Page');
    }

    public function forceDelete(User $user, Page $page): bool
    {
        if ($page->is_system) {
            return false;
        }

        return $user->can('ForceDelete:Page');
    }

    public function restore(User $user, Page $page): bool
    {
        return $user->can('Restore:Page');
    }

    public function forceDeleteAny(User $user): bool
    {
        return $user->can('ForceDeleteAny:Page');
    }

    public function restoreAny(User $user): bool
    {
        return $user->can('RestoreAny:Page');
    }

    public function replicate(User $user, Page $page): bool
    {
        return $user->can('Replicate:Page');
    }

    public function reorder(User $user): bool
    {
        return $user->can('Reorder:Page');
    }
}
