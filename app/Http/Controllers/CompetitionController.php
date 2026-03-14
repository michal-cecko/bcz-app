<?php

namespace App\Http\Controllers;

use App\Models\Competition;
use App\Models\Setting;
use App\Models\Team;
use Illuminate\View\View;

class CompetitionController extends Controller
{
    public function index(): View
    {
        $defaultTeamId = Setting::get('default_team_id');

        $competitions = Competition::query()
            ->where('is_published', true)
            ->with(['organizerTeam', 'disciplines'])
            ->orderByRaw('organizer_team_id = ? DESC', [$defaultTeamId])
            ->latest('date_start')
            ->paginate(12);

        return view('pages.competitions.index', [
            'competitions' => $competitions,
            'team' => null,
        ]);
    }

    public function show(Team $team, Competition $competition): View
    {
        abort_unless($competition->is_published, 404);
        abort_unless($competition->organizer_team_id === $team->id, 404);

        $competition->load([
            'organizerTeam',
            'disciplines',
            'athleteCategories',
            'timetableEntries',
            'registrationFees.athleteCategory',
            'rounds.parts',
            'rounds.athleteCategory',
        ]);

        return view('pages.competitions.show', compact('competition'));
    }
}
