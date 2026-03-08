<?php

namespace App\Policies;

use App\Enums\RoleEnum;
use App\Models\Membership;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class MembershipPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->hasRole([
            RoleEnum::SUPER_ADMIN,
            RoleEnum::OWNER,
            RoleEnum::ADMIN,
            RoleEnum::TEAM_ADMIN,
        ]);
    }

    public function view(User $user, Membership $membership): bool
    {
        return $user->hasRole([
            RoleEnum::SUPER_ADMIN,
            RoleEnum::OWNER,
            RoleEnum::ADMIN,
            RoleEnum::TEAM_ADMIN,
        ]);
    }

    public function create(User $user): bool
    {
        return $user->hasRole([
            RoleEnum::SUPER_ADMIN,
            RoleEnum::OWNER,
            RoleEnum::ADMIN,
            RoleEnum::TEAM_ADMIN,
        ]);
    }

    public function update(User $user, Membership $membership): bool
    {
        return $user->hasRole([
            RoleEnum::SUPER_ADMIN,
            RoleEnum::OWNER,
            RoleEnum::ADMIN,
        ]);
    }

    public function delete(User $user, Membership $membership): bool
    {
        return $user->hasRole([
            RoleEnum::SUPER_ADMIN,
            RoleEnum::OWNER,
        ]);
    }
}
