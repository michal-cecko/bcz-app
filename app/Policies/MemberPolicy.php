<?php

namespace App\Policies;

use App\Models\Member;
use App\Models\User;

class MemberPolicy
{
    public function view(User $user, Member $member): bool
    {
        return $member->user_id === $user->id;
    }

    public function create(User $user): bool
    {
        return (bool) config('family.enabled');
    }

    public function update(User $user, Member $member): bool
    {
        return config('family.enabled') && $this->view($user, $member);
    }

    public function delete(User $user, Member $member): bool
    {
        return false;
    }
}
