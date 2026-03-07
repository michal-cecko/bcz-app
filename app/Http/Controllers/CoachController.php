<?php

namespace App\Http\Controllers;

use App\Enums\RoleEnum;
use App\Models\User;
use Illuminate\View\View;

class CoachController extends Controller
{
    public function show(User $user): View
    {
        abort_unless($user->hasRole(RoleEnum::COACH), 404);

        $user->load([
            'coachProfile',
            'coachedTrainings' => fn ($query) => $query->where('is_active', true),
            'certifications',
        ]);

        return view('pages.coaches.show', compact('user'));
    }
}
