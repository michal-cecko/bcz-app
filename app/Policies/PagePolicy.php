<?php

namespace App\Policies;

use App\Enums\RoleEnum;
use App\Models\Page;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class PagePolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->hasRole([
            RoleEnum::SUPER_ADMIN,
            RoleEnum::ADMIN,
            RoleEnum::EDITOR,
        ]);
    }

    public function view(User $user, Page $page): bool
    {
        return $user->hasRole([
            RoleEnum::SUPER_ADMIN,
            RoleEnum::ADMIN,
            RoleEnum::EDITOR,
        ]);
    }

    public function create(User $user): bool
    {
        return $user->hasRole([
            RoleEnum::SUPER_ADMIN,
            RoleEnum::ADMIN,
            RoleEnum::EDITOR,
        ]);
    }

    public function update(User $user, Page $page): bool
    {
        return $user->hasRole([
            RoleEnum::SUPER_ADMIN,
            RoleEnum::ADMIN,
            RoleEnum::EDITOR,
        ]);
    }

    public function delete(User $user, Page $page): bool
    {
        if ($page->is_system) {
            return false;
        }

        return $user->hasRole([
            RoleEnum::SUPER_ADMIN,
            RoleEnum::ADMIN,
        ]);
    }

    public function forceDelete(User $user, Page $page): bool
    {
        if ($page->is_system) {
            return false;
        }

        return $user->hasRole([
            RoleEnum::SUPER_ADMIN,
        ]);
    }

    public function restore(User $user, Page $page): bool
    {
        return $user->hasRole([
            RoleEnum::SUPER_ADMIN,
            RoleEnum::ADMIN,
        ]);
    }
}
