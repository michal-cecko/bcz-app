<?php

namespace App\Policies;

use App\Enums\RoleEnum;
use App\Models\City;
use App\Models\User;

class CityPolicy
{
    protected function isAdmin(User $user): bool
    {
        return $user->hasRole([RoleEnum::SUPER_ADMIN, RoleEnum::ADMIN, RoleEnum::EDITOR]);
    }

    public function viewAny(User $user): bool
    {
        return $this->isAdmin($user);
    }

    public function view(User $user, City $city): bool
    {
        return $this->isAdmin($user);
    }

    public function create(User $user): bool
    {
        return $this->isAdmin($user);
    }

    public function update(User $user, City $city): bool
    {
        return $this->isAdmin($user);
    }

    public function delete(User $user, City $city): bool
    {
        return $this->isAdmin($user);
    }

    public function restore(User $user, City $city): bool
    {
        return $this->isAdmin($user);
    }

    public function forceDelete(User $user, City $city): bool
    {
        return $this->isAdmin($user);
    }
}
