<?php

namespace App\Policies;

use App\Enums\RoleEnum;
use App\Models\TeamPayout;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class TeamPayoutPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->hasRole([
            RoleEnum::SUPER_ADMIN,
            RoleEnum::ADMIN,
            RoleEnum::TEAM_ADMIN,
        ]);
    }

    public function view(User $user, TeamPayout $payout): bool
    {
        return $user->hasRole([
            RoleEnum::SUPER_ADMIN,
            RoleEnum::ADMIN,
            RoleEnum::TEAM_ADMIN,
        ]);
    }

    public function create(User $user): bool
    {
        return $user->hasRole([
            RoleEnum::SUPER_ADMIN,
            RoleEnum::ADMIN,
        ]);
    }

    public function update(User $user, TeamPayout $payout): bool
    {
        return $user->hasRole([
            RoleEnum::SUPER_ADMIN,
            RoleEnum::ADMIN,
        ]);
    }

    public function delete(User $user, TeamPayout $payout): bool
    {
        return $user->hasRole(RoleEnum::SUPER_ADMIN);
    }
}
