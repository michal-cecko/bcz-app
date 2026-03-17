<?php

namespace App\Policies;

use App\Models\Menu;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class MenuPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('ViewAny:Menu');
    }

    public function view(User $user, Menu $menu): bool
    {
        return $user->can('View:Menu');
    }

    public function create(User $user): bool
    {
        return $user->can('Create:Menu');
    }

    public function update(User $user, Menu $menu): bool
    {
        return $user->can('Update:Menu');
    }

    public function delete(User $user, Menu $menu): bool
    {
        return false;
    }

    public function restore(User $user, Menu $menu): bool
    {
        return false;
    }

    public function forceDelete(User $user, Menu $menu): bool
    {
        return false;
    }
}
