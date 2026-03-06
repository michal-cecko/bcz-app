<?php

namespace App\Policies;

use App\Enums\RoleEnum;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

    /**
     * SUPERADMIN + ADMIN + COACH + EDITOR + ATHLETE can see the list.
     * JUDGE + CUSTOMER cannot.
     */
    public function viewAny(User $authUser): bool
    {
        return $authUser->hasRole([
            RoleEnum::SuperAdmin,
            RoleEnum::Admin,
            RoleEnum::Coach,
            RoleEnum::Editor,
            RoleEnum::Athlete,
        ]);
    }

    /**
     * SUPERADMIN + ADMIN can view any user.
     * COACH + EDITOR can view any user.
     * ATHLETE can only view other athletes.
     */
    public function view(User $authUser, User $user): bool
    {
        if ($authUser->hasRole([RoleEnum::SuperAdmin, RoleEnum::Admin, RoleEnum::Coach, RoleEnum::Editor])) {
            return true;
        }

        if ($authUser->hasRole(RoleEnum::Athlete)) {
            return $user->hasRole(RoleEnum::Athlete);
        }

        return false;
    }

    /**
     * Only SUPERADMIN + ADMIN can create users.
     */
    public function create(User $authUser): bool
    {
        return $authUser->hasRole([RoleEnum::SuperAdmin, RoleEnum::Admin]);
    }

    /**
     * SUPERADMIN can update anyone.
     * ADMIN can update anyone except other ADMINs/SUPERADMINs (self-edit allowed).
     */
    public function update(User $authUser, User $user): bool
    {
        if ($authUser->hasRole(RoleEnum::SuperAdmin)) {
            return true;
        }

        if ($authUser->hasRole(RoleEnum::Admin)) {
            if ($authUser->id === $user->id) {
                return true;
            }

            return ! $user->hasRole([RoleEnum::Admin, RoleEnum::SuperAdmin]);
        }

        return false;
    }

    /**
     * SUPERADMIN can delete anyone (except self).
     * ADMIN can delete non-ADMIN/non-SUPERADMIN users (not self).
     */
    public function delete(User $authUser, User $user): bool
    {
        if ($authUser->id === $user->id) {
            return false;
        }

        if ($authUser->hasRole(RoleEnum::SuperAdmin)) {
            return true;
        }

        if ($authUser->hasRole(RoleEnum::Admin)) {
            return ! $user->hasRole([RoleEnum::Admin, RoleEnum::SuperAdmin]);
        }

        return false;
    }

    public function restore(User $authUser, User $user): bool
    {
        return $authUser->hasRole([RoleEnum::SuperAdmin, RoleEnum::Admin]);
    }

    public function forceDelete(User $authUser, User $user): bool
    {
        return $authUser->hasRole(RoleEnum::SuperAdmin);
    }

    public function forceDeleteAny(User $authUser): bool
    {
        return $authUser->hasRole(RoleEnum::SuperAdmin);
    }

    public function restoreAny(User $authUser): bool
    {
        return $authUser->hasRole([RoleEnum::SuperAdmin, RoleEnum::Admin]);
    }

    public function replicate(User $authUser, User $user): bool
    {
        return $authUser->hasRole([RoleEnum::SuperAdmin, RoleEnum::Admin]);
    }

    public function reorder(User $authUser): bool
    {
        return $authUser->hasRole(RoleEnum::SuperAdmin);
    }
}
