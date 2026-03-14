<?php

namespace App\Policies;

use App\Enums\RoleEnum;
use App\Models\Menu;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class MenuPolicy
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

    public function view(User $user, Menu $menu): bool
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
        ]);
    }

    public function update(User $user, Menu $menu): bool
    {
        return $user->hasRole([
            RoleEnum::SUPER_ADMIN,
            RoleEnum::ADMIN,
            RoleEnum::EDITOR,
        ]);
    }

    public function delete(User $user, Menu $menu): bool
    {
        return false;
    }
}
