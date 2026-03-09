<?php

namespace App\Http\Controllers;

use App\Models\Team;
use Illuminate\Contracts\View\View;

class TeamController extends Controller
{
    public function show(Team $team): View
    {
        $team->load([
            'members',
            'organizedCompetitions',
            'trainings',
            'events',
        ]);

        return view('pages.team-detail', [
            'team' => $team,
        ]);
    }

    public function trainings(Team $team): View
    {
        $trainings = $team->trainings()
            ->where('is_active', true)
            ->with(['sportCategory', 'coaches'])
            ->orderBy('sort_order')
            ->get();

        return view('pages.trainings.index', compact('trainings', 'team'));
    }

    public function competitions(Team $team): View
    {
        $competitions = $team->organizedCompetitions()
            ->where('is_published', true)
            ->with(['disciplines'])
            ->latest('date_start')
            ->paginate(12);

        return view('pages.competitions.index', compact('competitions', 'team'));
    }

    public function members(Team $team): View
    {
        $team->load('members');

        return view('pages.team-members', compact('team'));
    }
}
