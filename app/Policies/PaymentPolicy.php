<?php

namespace App\Policies;

use App\Enums\RoleEnum;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class PaymentPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->hasAnyAppRole([
            RoleEnum::SUPER_ADMIN,
            RoleEnum::ADMIN,
            RoleEnum::TEAM_ADMIN,
        ]);
    }

    public function view(User $user, Payment $payment): bool
    {
        return $user->hasAnyAppRole([
            RoleEnum::SUPER_ADMIN,
            RoleEnum::ADMIN,
            RoleEnum::TEAM_ADMIN,
        ]);
    }

    public function create(User $user): bool
    {
        return $user->hasAnyAppRole([
            RoleEnum::SUPER_ADMIN,
            RoleEnum::ADMIN,
            RoleEnum::TEAM_ADMIN,
        ]);
    }

    public function update(User $user, Payment $payment): bool
    {
        return $user->hasRole([
            RoleEnum::SUPER_ADMIN,
            RoleEnum::ADMIN,
        ]);
    }

    public function delete(User $user, Payment $payment): bool
    {
        return $user->hasRole([
            RoleEnum::SUPER_ADMIN,
            RoleEnum::ADMIN,
        ]);
    }
}
