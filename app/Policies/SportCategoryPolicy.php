<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\SportCategory;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;

class SportCategoryPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:SportCategory');
    }

    public function view(AuthUser $authUser, SportCategory $sportCategory): bool
    {
        return $authUser->can('View:SportCategory');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:SportCategory');
    }

    public function update(AuthUser $authUser, SportCategory $sportCategory): bool
    {
        return $authUser->can('Update:SportCategory');
    }

    public function delete(AuthUser $authUser, SportCategory $sportCategory): bool
    {
        return $authUser->can('Delete:SportCategory');
    }

    public function restore(AuthUser $authUser, SportCategory $sportCategory): bool
    {
        return $authUser->can('Restore:SportCategory');
    }

    public function forceDelete(AuthUser $authUser, SportCategory $sportCategory): bool
    {
        return $authUser->can('ForceDelete:SportCategory');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:SportCategory');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:SportCategory');
    }

    public function replicate(AuthUser $authUser, SportCategory $sportCategory): bool
    {
        return $authUser->can('Replicate:SportCategory');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:SportCategory');
    }
}
