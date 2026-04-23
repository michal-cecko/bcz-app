<?php

namespace App\Policies;

use App\Enums\RoleEnum;
use App\Models\PaymentMethod;
use App\Models\User;

class PaymentMethodPolicy
{
    protected function isAdmin(User $user): bool
    {
        return $user->hasRole([RoleEnum::SUPER_ADMIN, RoleEnum::ADMIN]);
    }

    public function viewAny(User $user): bool
    {
        return $this->isAdmin($user);
    }

    public function view(User $user, PaymentMethod $paymentMethod): bool
    {
        return $this->isAdmin($user);
    }

    public function create(User $user): bool
    {
        return $this->isAdmin($user);
    }

    public function update(User $user, PaymentMethod $paymentMethod): bool
    {
        return $this->isAdmin($user);
    }

    public function delete(User $user, PaymentMethod $paymentMethod): bool
    {
        return $this->isAdmin($user);
    }

    public function restore(User $user, PaymentMethod $paymentMethod): bool
    {
        return $this->isAdmin($user);
    }

    public function forceDelete(User $user, PaymentMethod $paymentMethod): bool
    {
        return $this->isAdmin($user);
    }
}
