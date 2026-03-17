<?php

namespace App\Policies;

use App\Models\EmailTemplate;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class EmailTemplatePolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('ViewAny:EmailTemplate');
    }

    public function view(User $user, EmailTemplate $emailTemplate): bool
    {
        return $user->can('View:EmailTemplate');
    }

    public function create(User $user): bool
    {
        return $user->can('Create:EmailTemplate');
    }

    public function update(User $user, EmailTemplate $emailTemplate): bool
    {
        if ($user->can('Update:EmailTemplate')) {
            return true;
        }

        return $user->can('UpdateOwn:EmailTemplate') && $emailTemplate->created_by === $user->id;
    }

    public function delete(User $user, EmailTemplate $emailTemplate): bool
    {
        if ($user->can('Delete:EmailTemplate')) {
            return true;
        }

        return $user->can('DeleteOwn:EmailTemplate') && $emailTemplate->created_by === $user->id;
    }

    public function restore(User $user, EmailTemplate $emailTemplate): bool
    {
        return $user->can('Restore:EmailTemplate');
    }

    public function forceDelete(User $user, EmailTemplate $emailTemplate): bool
    {
        return $user->can('ForceDelete:EmailTemplate');
    }

    public function forceDeleteAny(User $user): bool
    {
        return $user->can('ForceDeleteAny:EmailTemplate');
    }

    public function restoreAny(User $user): bool
    {
        return $user->can('RestoreAny:EmailTemplate');
    }

    public function replicate(User $user, EmailTemplate $emailTemplate): bool
    {
        return $user->can('Replicate:EmailTemplate');
    }

    public function reorder(User $user): bool
    {
        return $user->can('Reorder:EmailTemplate');
    }
}
