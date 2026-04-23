<?php

namespace App\Policies;

use App\Models\Payment;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class PaymentPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('ViewAny:Payment');
    }

    public function view(User $user, Payment $payment): bool
    {
        if ($payment->user_id === $user->id) {
            return true;
        }

        return $user->can('ViewAny:Payment');
    }

    public function create(User $user): bool
    {
        return $user->can('Create:Payment');
    }

    public function update(User $user, Payment $payment): bool
    {
        return $user->can('Update:Payment');
    }

    public function delete(User $user, Payment $payment): bool
    {
        return $user->can('Delete:Payment');
    }

    public function restore(User $user, Payment $payment): bool
    {
        return $user->can('Restore:Payment');
    }

    public function forceDelete(User $user, Payment $payment): bool
    {
        return $user->can('ForceDelete:Payment');
    }
}
