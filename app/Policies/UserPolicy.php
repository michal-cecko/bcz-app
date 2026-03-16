<?php

namespace App\Policies;

use App\Enums\RoleEnum;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

    /**
     * SUPERADMIN + ADMIN + TEAMADMIN + COACH + EDITOR + ATHLETE can see the list.
     * JUDGE cannot.
     */
    public function viewAny(User $authUser): bool
    {
        return $authUser->hasAnyAppRole([
            RoleEnum::SUPER_ADMIN,
            RoleEnum::ADMIN,
            RoleEnum::TEAM_ADMIN,
            RoleEnum::COACH,
            RoleEnum::EDITOR,
            RoleEnum::ATHLETE,
        ]);
    }

    /**
     * SUPERADMIN + ADMIN can view any user.
     * TEAMADMIN + COACH + EDITOR can view any user.
     * ATHLETE can only view other athletes.
     */
    public function view(User $authUser, User $user): bool
    {
        if ($authUser->hasAnyAppRole([RoleEnum::SUPER_ADMIN, RoleEnum::ADMIN, RoleEnum::TEAM_ADMIN, RoleEnum::COACH, RoleEnum::EDITOR])) {
            return true;
        }

        if ($authUser->hasAnyAppRole([RoleEnum::ATHLETE])) {
            return $user->hasAnyAppRole([RoleEnum::ATHLETE]);
        }

        return false;
    }

    /**
     * Only SUPERADMIN + ADMIN can create users.
     */
    public function create(User $authUser): bool
    {
        return $authUser->hasRole([RoleEnum::SUPER_ADMIN, RoleEnum::ADMIN]);
    }

    /**
     * SUPERADMIN can update anyone.
     * ADMIN can update anyone except SUPERADMIN (self-edit allowed).
     */
    public function update(User $authUser, User $user): bool
    {
        if ($authUser->hasRole(RoleEnum::SUPER_ADMIN)) {
            return true;
        }

        if ($authUser->hasRole(RoleEnum::ADMIN)) {
            if ($authUser->id === $user->id) {
                return true;
            }

            return ! $user->hasRole([RoleEnum::ADMIN, RoleEnum::SUPER_ADMIN]);
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

        if ($authUser->hasRole(RoleEnum::SUPER_ADMIN)) {
            return true;
        }

        if ($authUser->hasRole(RoleEnum::ADMIN)) {
            return ! $user->hasRole([RoleEnum::ADMIN, RoleEnum::SUPER_ADMIN]);
        }

        return false;
    }

    public function restore(User $authUser, User $user): bool
    {
        return $authUser->hasRole([RoleEnum::SUPER_ADMIN, RoleEnum::ADMIN]);
    }

    public function forceDelete(User $authUser, User $user): bool
    {
        return $authUser->hasRole(RoleEnum::SUPER_ADMIN);
    }

    public function forceDeleteAny(User $authUser): bool
    {
        return $authUser->hasRole(RoleEnum::SUPER_ADMIN);
    }

    public function restoreAny(User $authUser): bool
    {
        return $authUser->hasRole([RoleEnum::SUPER_ADMIN, RoleEnum::ADMIN]);
    }

    public function replicate(User $authUser, User $user): bool
    {
        return $authUser->hasRole([RoleEnum::SUPER_ADMIN, RoleEnum::ADMIN]);
    }

    public function reorder(User $authUser): bool
    {
        return $authUser->hasRole(RoleEnum::SUPER_ADMIN);
    }
}
