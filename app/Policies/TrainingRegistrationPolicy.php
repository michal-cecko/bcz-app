<?php

namespace App\Policies;

use App\Enums\RoleEnum;
use App\Models\TrainingRegistration;
use App\Models\User;

class TrainingRegistrationPolicy
{
    protected function isGlobalAdmin(User $user): bool
    {
        return $user->hasRole([RoleEnum::SUPER_ADMIN, RoleEnum::ADMIN, RoleEnum::EDITOR]);
    }

    protected function canManageTeam(User $user, TrainingRegistration $registration): bool
    {
        $teamId = $registration->training?->team_id;
        if (! $teamId) {
            return false;
        }

        return $user->teams()
            ->where('teams.id', $teamId)
            ->wherePivotIn('role', [RoleEnum::TEAM_ADMIN->value, RoleEnum::COACH->value])
            ->exists();
    }

    public function viewAny(User $user): bool
    {
        return $user->hasAnyAppRole([
            RoleEnum::SUPER_ADMIN, RoleEnum::ADMIN, RoleEnum::EDITOR,
            RoleEnum::TEAM_ADMIN, RoleEnum::COACH,
        ]);
    }

    public function view(User $user, TrainingRegistration $registration): bool
    {
        if ($this->isGlobalAdmin($user)) {
            return true;
        }

        if ($registration->user_id === $user->id) {
            return true;
        }

        return $this->canManageTeam($user, $registration);
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, TrainingRegistration $registration): bool
    {
        if ($this->isGlobalAdmin($user)) {
            return true;
        }

        return $this->canManageTeam($user, $registration);
    }

    public function delete(User $user, TrainingRegistration $registration): bool
    {
        if ($this->isGlobalAdmin($user)) {
            return true;
        }

        return $this->canManageTeam($user, $registration);
    }

    public function restore(User $user, TrainingRegistration $registration): bool
    {
        return $this->isGlobalAdmin($user);
    }

    public function forceDelete(User $user, TrainingRegistration $registration): bool
    {
        return $this->isGlobalAdmin($user);
    }
}
