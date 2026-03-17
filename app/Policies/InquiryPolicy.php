<?php

namespace App\Policies;

use App\Models\Inquiry;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class InquiryPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('ViewAny:Inquiry');
    }

    public function view(User $user, Inquiry $inquiry): bool
    {
        return $user->can('View:Inquiry');
    }

    public function create(User $user): bool
    {
        return $user->can('Create:Inquiry');
    }

    public function update(User $user, Inquiry $inquiry): bool
    {
        if ($user->can('Update:Inquiry')) {
            return true;
        }

        return $user->can('UpdateOwn:Inquiry') && $inquiry->created_by === $user->id;
    }

    public function delete(User $user, Inquiry $inquiry): bool
    {
        if ($user->can('Delete:Inquiry')) {
            return true;
        }

        return $user->can('DeleteOwn:Inquiry') && $inquiry->created_by === $user->id;
    }

    public function restore(User $user, Inquiry $inquiry): bool
    {
        return $user->can('Restore:Inquiry');
    }

    public function forceDelete(User $user, Inquiry $inquiry): bool
    {
        return $user->can('ForceDelete:Inquiry');
    }

    public function forceDeleteAny(User $user): bool
    {
        return $user->can('ForceDeleteAny:Inquiry');
    }

    public function restoreAny(User $user): bool
    {
        return $user->can('RestoreAny:Inquiry');
    }

    public function replicate(User $user, Inquiry $inquiry): bool
    {
        return $user->can('Replicate:Inquiry');
    }

    public function reorder(User $user): bool
    {
        return $user->can('Reorder:Inquiry');
    }
}
