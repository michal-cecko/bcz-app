<?php

namespace App\Policies;

use App\Enums\RoleEnum;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class PaymentPolicy
{
    use HandlesAuthorization;

    protected function isGlobalAdmin(User $user): bool
    {
        return $user->hasRole([RoleEnum::SUPER_ADMIN, RoleEnum::ADMIN, RoleEnum::EDITOR]);
    }

    protected function isTeamAdminOf(User $user, ?string $teamId): bool
    {
        if (! $teamId) {
            return false;
        }

        return $user->teams()
            ->where('teams.id', $teamId)
            ->wherePivot('role', RoleEnum::TEAM_ADMIN->value)
            ->exists();
    }

    public function viewAny(User $user): bool
    {
        return $user->can('ViewAny:Payment');
    }

    public function view(User $user, Payment $payment): bool
    {
        if ($payment->user_id === $user->id) {
            return true;
        }

        if (! $user->can('View:Payment')) {
            return false;
        }

        return $this->isGlobalAdmin($user) || $this->isTeamAdminOf($user, $payment->team_id);
    }

    public function create(User $user): bool
    {
        return $user->can('Create:Payment');
    }

    public function update(User $user, Payment $payment): bool
    {
        if (! $user->can('Update:Payment')) {
            return false;
        }

        return $this->isGlobalAdmin($user) || $this->isTeamAdminOf($user, $payment->team_id);
    }

    public function delete(User $user, Payment $payment): bool
    {
        if (! $user->can('Delete:Payment')) {
            return false;
        }

        return $this->isGlobalAdmin($user) || $this->isTeamAdminOf($user, $payment->team_id);
    }

    public function restore(User $user, Payment $payment): bool
    {
        return $user->can('Restore:Payment') && $this->isGlobalAdmin($user);
    }

    public function forceDelete(User $user, Payment $payment): bool
    {
        return $user->can('ForceDelete:Payment') && $this->isGlobalAdmin($user);
    }
}
