<?php

namespace App\Policies;

use App\Enums\RoleEnum;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

    protected function sharesTeamWithTeamAdminActor(User $actor, User $target): bool
    {
        $actorTeamAdminTeamIds = $actor->teams()
            ->wherePivot('role', RoleEnum::TEAM_ADMIN->value)
            ->pluck('teams.id');

        if ($actorTeamAdminTeamIds->isEmpty()) {
            return false;
        }

        return $target->teams()
            ->whereIn('teams.id', $actorTeamAdminTeamIds)
            ->exists();
    }

    public function viewAny(User $authUser): bool
    {
        return $authUser->can('ViewAny:User');
    }

    /**
     * Self-view always allowed. Permission check + ATHLETE can only view other athletes.
     */
    public function view(User $authUser, User $user): bool
    {
        if ($authUser->id === $user->id) {
            return true;
        }

        if (! $authUser->can('View:User')) {
            return false;
        }

        if ($authUser->hasAnyAppRole([RoleEnum::ATHLETE]) && ! $authUser->hasAnyAppRole([
            RoleEnum::SUPER_ADMIN, RoleEnum::ADMIN, RoleEnum::TEAM_ADMIN, RoleEnum::COACH, RoleEnum::EDITOR,
        ])) {
            return $user->hasAnyAppRole([RoleEnum::ATHLETE]);
        }

        return true;
    }

    public function create(User $authUser): bool
    {
        return $authUser->can('Create:User');
    }

    /**
     * Self-edit always allowed. Permission check + ADMIN cannot modify SUPERADMIN.
     * TEAM_ADMIN can only edit users who share a team where actor has TEAM_ADMIN pivot.
     */
    public function update(User $authUser, User $user): bool
    {
        if ($authUser->id === $user->id) {
            return true;
        }

        if (! $authUser->can('Update:User')) {
            return false;
        }

        if ($authUser->hasRole(RoleEnum::SUPER_ADMIN)) {
            return true;
        }

        if ($authUser->hasRole(RoleEnum::ADMIN)) {
            return ! $user->hasRole([RoleEnum::ADMIN, RoleEnum::SUPER_ADMIN]);
        }

        if ($authUser->teams()->wherePivot('role', RoleEnum::TEAM_ADMIN->value)->exists()) {
            return $this->sharesTeamWithTeamAdminActor($authUser, $user);
        }

        return true;
    }

    /**
     * Permission check + hierarchy: no self-delete, ADMIN cannot delete ADMIN/SUPERADMIN.
     * TEAM_ADMIN can only delete users who share a team where actor has TEAM_ADMIN pivot.
     */
    public function delete(User $authUser, User $user): bool
    {
        if ($authUser->id === $user->id) {
            return false;
        }

        if (! $authUser->can('Delete:User')) {
            return false;
        }

        if ($authUser->hasRole(RoleEnum::SUPER_ADMIN)) {
            return true;
        }

        if ($authUser->hasRole(RoleEnum::ADMIN)) {
            return ! $user->hasRole([RoleEnum::ADMIN, RoleEnum::SUPER_ADMIN]);
        }

        if ($authUser->teams()->wherePivot('role', RoleEnum::TEAM_ADMIN->value)->exists()) {
            return $this->sharesTeamWithTeamAdminActor($authUser, $user);
        }

        return true;
    }

    public function restore(User $authUser, User $user): bool
    {
        return $authUser->can('Restore:User');
    }

    public function forceDelete(User $authUser, User $user): bool
    {
        return $authUser->can('ForceDelete:User');
    }

    public function forceDeleteAny(User $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:User');
    }

    public function restoreAny(User $authUser): bool
    {
        return $authUser->can('RestoreAny:User');
    }

    public function replicate(User $authUser, User $user): bool
    {
        return $authUser->can('Replicate:User');
    }

    public function reorder(User $authUser): bool
    {
        return $authUser->can('Reorder:User');
    }
}
