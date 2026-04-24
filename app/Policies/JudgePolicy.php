<?php

namespace App\Policies;

use App\Enums\RoleEnum;
use App\Models\Judge;
use App\Models\User;

class JudgePolicy
{
    protected function isAdmin(User $user): bool
    {
        return $user->hasRole([RoleEnum::SUPER_ADMIN, RoleEnum::ADMIN, RoleEnum::EDITOR]);
    }

    public function viewAny(User $user): bool
    {
        return $this->isAdmin($user);
    }

    public function view(User $user, Judge $judge): bool
    {
        return $this->isAdmin($user);
    }

    public function create(User $user): bool
    {
        return $this->isAdmin($user);
    }

    public function update(User $user, Judge $judge): bool
    {
        return $this->isAdmin($user);
    }

    public function delete(User $user, Judge $judge): bool
    {
        return $this->isAdmin($user);
    }

    public function restore(User $user, Judge $judge): bool
    {
        return $this->isAdmin($user);
    }

    public function forceDelete(User $user, Judge $judge): bool
    {
        return $this->isAdmin($user);
    }
}
