<?php

namespace App\Http\Controllers;

use App\Models\Competition;

class CompetitionController extends Controller
{
    public function index(): \Illuminate\View\View
    {
        $competitions = Competition::query()
            ->where('is_published', true)
            ->with('organizerTeam', 'disciplines', 'athleteCategories')
            ->latest('date_start')
            ->paginate(12);

        return view('competitions.index', compact('competitions'));
    }

    public function show(Competition $competition): \Illuminate\View\View
    {
        abort_unless($competition->is_published, 404);

        $competition->load([
            'organizerTeam',
            'disciplines',
            'athleteCategories',
            'timetableEntries',
            'registrationFees.athleteCategory',
            'rounds.parts',
            'rounds.athleteCategory',
        ]);

        return view('competitions.show', compact('competition'));
    }
}
