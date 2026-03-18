<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use App\Models\Team;
use App\Models\Training;
use Illuminate\View\View;

class TrainingController extends Controller
{
    public function index(): View
    {
        $defaultTeamId = Setting::get('default_team_id');

        $trainings = Training::query()
            ->where('is_active', true)
            ->with(['sportCategory', 'coaches', 'team'])
            ->orderByRaw('team_id = ? DESC', [$defaultTeamId])
            ->orderBy('sort_order')
            ->get();

        return view('pages.trainings.index', [
            'trainings' => $trainings,
            'team' => null,
        ]);
    }

    public function show(Team $team, Training $training): View
    {
        abort_unless($training->team_id === $team->id, 404);

        $training->load(['sportCategory', 'coaches.coachProfile', 'coaches.certifications', 'team', 'city'])
            ->loadCount('registrations');

        return view('pages.trainings.show', compact('training'));
    }
}
