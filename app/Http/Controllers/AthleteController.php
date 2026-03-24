<?php

namespace App\Http\Controllers;

use App\Enums\RoleEnum;
use App\Models\User;
use Illuminate\View\View;

class AthleteController extends Controller
{
    public function show(User $user): View
    {
        abort_unless(
            $user->teams()->wherePivot('role', RoleEnum::ATHLETE->value)->exists(),
            404
        );

        $user->load([
            'athleteProfile',
            'athleteExercises' => fn ($q) => $q->orderBy('sort_order')->with(['exercise', 'media']),
            'athleteGoals',
            'certifications',
            'competitionResults.roundPart.competitionRound.competitionDetail.event',
        ]);

        return view('pages.athletes.show', compact('user'));
    }
}
