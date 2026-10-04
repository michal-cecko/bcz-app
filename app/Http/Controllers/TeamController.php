<?php

namespace App\Http\Controllers;

use App\Models\Team;
use Illuminate\Contracts\View\View;

class TeamController extends Controller
{
    public function index(): View
    {
        $teamCount = Team::where('is_active', true)->count();

        return view('pages.teams.index', compact('teamCount'));
    }

    public function show(Team $team): View
    {
        $team->load([
            'competitions',
            'trainings',
            'events',
        ]);

        return view('pages.team-detail', [
            'team' => $team,
            'members' => $team->publicMembers()->get()->unique('id')->values(),
        ]);
    }

    public function trainings(Team $team): View
    {
        $trainings = $team->trainings()
            ->where('is_active', true)
            ->with(['media', 'sportCategory.media', 'coaches', 'team'])
            ->orderBy('sort_order')
            ->get();

        return view('pages.trainings.index', compact('trainings', 'team'));
    }

    public function competitions(Team $team): View
    {
        $competitions = $team->competitions()
            ->where('is_published', true)
            ->with(['eventCategory', 'team', 'competitionDetail.disciplines'])
            ->latest('date')
            ->paginate(12);

        return view('pages.competitions.index', compact('competitions', 'team'));
    }

    public function members(Team $team): View
    {
        return view('pages.team-members', [
            'team' => $team,
            'members' => $team->publicMembers()->get()->unique('id')->values(),
        ]);
    }
}
