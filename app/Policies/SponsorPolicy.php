<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Sponsor;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;

class SponsorPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Sponsor');
    }

    public function view(AuthUser $authUser, Sponsor $sponsor): bool
    {
        return $authUser->can('View:Sponsor');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Sponsor');
    }

    public function update(AuthUser $authUser, Sponsor $sponsor): bool
    {
        return $authUser->can('Update:Sponsor');
    }

    public function delete(AuthUser $authUser, Sponsor $sponsor): bool
    {
        return $authUser->can('Delete:Sponsor');
    }

    public function restore(AuthUser $authUser, Sponsor $sponsor): bool
    {
        return $authUser->can('Restore:Sponsor');
    }

    public function forceDelete(AuthUser $authUser, Sponsor $sponsor): bool
    {
        return $authUser->can('ForceDelete:Sponsor');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:Sponsor');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:Sponsor');
    }

    public function replicate(AuthUser $authUser, Sponsor $sponsor): bool
    {
        return $authUser->can('Replicate:Sponsor');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:Sponsor');
    }
}
