<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Discipline;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;

class DisciplinePolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Discipline');
    }

    public function view(AuthUser $authUser, Discipline $discipline): bool
    {
        return $authUser->can('View:Discipline');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Discipline');
    }

    public function update(AuthUser $authUser, Discipline $discipline): bool
    {
        return $authUser->can('Update:Discipline');
    }

    public function delete(AuthUser $authUser, Discipline $discipline): bool
    {
        return $authUser->can('Delete:Discipline');
    }

    public function restore(AuthUser $authUser, Discipline $discipline): bool
    {
        return $authUser->can('Restore:Discipline');
    }

    public function forceDelete(AuthUser $authUser, Discipline $discipline): bool
    {
        return $authUser->can('ForceDelete:Discipline');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:Discipline');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:Discipline');
    }

    public function replicate(AuthUser $authUser, Discipline $discipline): bool
    {
        return $authUser->can('Replicate:Discipline');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:Discipline');
    }
}
