<?php

namespace App\Http\Controllers;

use App\Enums\EventTypeEnum;
use App\Models\Event;
use App\Models\Team;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CompetitionController extends Controller
{
    public function index(): View
    {
        $allCompetitions = Event::query()
            ->where('event_type', EventTypeEnum::Competition)
            ->where('is_published', true)
            ->with(['eventCategory', 'team', 'organization', 'competitionDetail.disciplines', 'competitionDetail.timetableEntries', 'media'])
            ->orderBy('date')
            ->get();

        $upcoming = $allCompetitions->filter(fn (Event $e) => $e->status !== 'finished')->sortBy('date')->values();
        $finished = $allCompetitions->filter(fn (Event $e) => $e->status === 'finished')->sortByDesc('date')->take(8)->values();

        $athletes = User::query()
            ->whereNotNull('athlete_profile_approved_at')
            ->whereHas('athleteProfile')
            ->with(['athleteProfile', 'media'])
            ->inRandomOrder()
            ->limit(5)
            ->get();

        return view('pages.competitions.index-dedicated', compact('upcoming', 'finished', 'athletes'));
    }

    public function show(Team $team, Event $event): RedirectResponse
    {
        return redirect()->route('event.show', $event, 301);
    }
}
