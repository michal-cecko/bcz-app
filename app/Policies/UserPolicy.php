<?php

namespace App\Policies;

use App\Enums\RoleEnum;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $authUser): bool
    {
        return $authUser->can('ViewAny:User');
    }

    /**
     * Permission check + ATHLETE can only view other athletes.
     */
    public function view(User $authUser, User $user): bool
    {
        if (! $authUser->can('View:User')) {
            return false;
        }

        if ($authUser->hasAnyAppRole([RoleEnum::ATHLETE]) && ! $authUser->hasAnyAppRole([
            RoleEnum::SUPER_ADMIN, RoleEnum::ADMIN, RoleEnum::TEAM_ADMIN, RoleEnum::COACH, RoleEnum::EDITOR,
        ])) {
            return $user->hasAnyAppRole([RoleEnum::ATHLETE]);
        }

        return true;
    }

    public function create(User $authUser): bool
    {
        return $authUser->can('Create:User');
    }

    /**
     * Permission check + ADMIN cannot modify SUPERADMIN, self-edit allowed.
     */
    public function update(User $authUser, User $user): bool
    {
        if (! $authUser->can('Update:User')) {
            return false;
        }

        if ($authUser->hasRole(RoleEnum::SUPER_ADMIN)) {
            return true;
        }

        if ($authUser->hasRole(RoleEnum::ADMIN)) {
            if ($authUser->id === $user->id) {
                return true;
            }

            return ! $user->hasRole([RoleEnum::ADMIN, RoleEnum::SUPER_ADMIN]);
        }

        return true;
    }

    /**
     * Permission check + hierarchy: no self-delete, ADMIN cannot delete ADMIN/SUPERADMIN.
     */
    public function delete(User $authUser, User $user): bool
    {
        if ($authUser->id === $user->id) {
            return false;
        }

        if (! $authUser->can('Delete:User')) {
            return false;
        }

        if ($authUser->hasRole(RoleEnum::SUPER_ADMIN)) {
            return true;
        }

        if ($authUser->hasRole(RoleEnum::ADMIN)) {
            return ! $user->hasRole([RoleEnum::ADMIN, RoleEnum::SUPER_ADMIN]);
        }

        return true;
    }

    public function restore(User $authUser, User $user): bool
    {
        return $authUser->can('Restore:User');
    }

    public function forceDelete(User $authUser, User $user): bool
    {
        return $authUser->can('ForceDelete:User');
    }

    public function forceDeleteAny(User $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:User');
    }

    public function restoreAny(User $authUser): bool
    {
        return $authUser->can('RestoreAny:User');
    }

    public function replicate(User $authUser, User $user): bool
    {
        return $authUser->can('Replicate:User');
    }

    public function reorder(User $authUser): bool
    {
        return $authUser->can('Reorder:User');
    }
}
