<?php

namespace App\Policies;

use App\Enums\RoleEnum;
use App\Models\TeamSeason;
use App\Models\User;

class TeamSeasonPolicy
{
    protected function isGlobalAdmin(User $user): bool
    {
        return $user->hasRole([RoleEnum::SUPER_ADMIN, RoleEnum::ADMIN, RoleEnum::EDITOR]);
    }

    protected function isTeamAdmin(User $user, TeamSeason $season): bool
    {
        if (! $season->team_id) {
            return false;
        }

        return $user->teams()
            ->where('teams.id', $season->team_id)
            ->wherePivot('role', RoleEnum::TEAM_ADMIN->value)
            ->exists();
    }

    public function viewAny(User $user): bool
    {
        return $user->hasAnyAppRole([
            RoleEnum::SUPER_ADMIN, RoleEnum::ADMIN, RoleEnum::EDITOR, RoleEnum::TEAM_ADMIN,
        ]);
    }

    public function view(User $user, TeamSeason $season): bool
    {
        return $this->isGlobalAdmin($user) || $this->isTeamAdmin($user, $season);
    }

    public function create(User $user): bool
    {
        return $user->hasAnyAppRole([
            RoleEnum::SUPER_ADMIN, RoleEnum::ADMIN, RoleEnum::EDITOR, RoleEnum::TEAM_ADMIN,
        ]);
    }

    public function update(User $user, TeamSeason $season): bool
    {
        return $this->isGlobalAdmin($user) || $this->isTeamAdmin($user, $season);
    }

    public function delete(User $user, TeamSeason $season): bool
    {
        return $this->isGlobalAdmin($user) || $this->isTeamAdmin($user, $season);
    }

    public function restore(User $user, TeamSeason $season): bool
    {
        return $this->isGlobalAdmin($user);
    }

    public function forceDelete(User $user, TeamSeason $season): bool
    {
        return $this->isGlobalAdmin($user);
    }
}
