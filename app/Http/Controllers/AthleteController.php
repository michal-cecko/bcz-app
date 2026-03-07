<?php

namespace App\Http\Controllers;

use App\Enums\RoleEnum;
use App\Models\User;
use Illuminate\View\View;

class AthleteController extends Controller
{
    public function show(User $user): View
    {
        abort_unless($user->hasRole(RoleEnum::ATHLETE), 404);

        $user->load([
            'athleteProfile',
            'athleteExercises.exercise',
            'athleteGoals',
            'certifications',
        ]);

        return view('pages.athletes.show', compact('user'));
    }
}
