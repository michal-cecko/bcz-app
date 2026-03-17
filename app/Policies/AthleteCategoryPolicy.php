<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\AthleteCategory;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;

class AthleteCategoryPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:AthleteCategory');
    }

    public function view(AuthUser $authUser, AthleteCategory $athleteCategory): bool
    {
        return $authUser->can('View:AthleteCategory');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:AthleteCategory');
    }

    public function update(AuthUser $authUser, AthleteCategory $athleteCategory): bool
    {
        return $authUser->can('Update:AthleteCategory');
    }

    public function delete(AuthUser $authUser, AthleteCategory $athleteCategory): bool
    {
        return $authUser->can('Delete:AthleteCategory');
    }

    public function restore(AuthUser $authUser, AthleteCategory $athleteCategory): bool
    {
        return $authUser->can('Restore:AthleteCategory');
    }

    public function forceDelete(AuthUser $authUser, AthleteCategory $athleteCategory): bool
    {
        return $authUser->can('ForceDelete:AthleteCategory');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:AthleteCategory');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:AthleteCategory');
    }

    public function replicate(AuthUser $authUser, AthleteCategory $athleteCategory): bool
    {
        return $authUser->can('Replicate:AthleteCategory');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:AthleteCategory');
    }
}
