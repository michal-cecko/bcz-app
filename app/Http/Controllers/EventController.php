<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Setting;
use Illuminate\View\View;

class EventController extends Controller
{
    public function index(): View
    {
        $defaultTeamId = Setting::get('default_team_id');

        $events = Event::query()
            ->where('team_id', $defaultTeamId)
            ->where('is_published', true)
            ->with(['eventCategory', 'team', 'organization'])
            ->latest('date')
            ->paginate(12);

        return view('pages.events.index', compact('events'));
    }

    public function show(Event $event): View
    {
        abort_unless($event->is_published, 404);

        $event->load([
            'eventCategory',
            'team',
            'organization',
            'competitionDetail.disciplines',
            'competitionDetail.athleteCategories',
            'competitionDetail.timetableEntries',
            'competitionDetail.registrationFees.athleteCategory',
            'competitionDetail.rounds.parts',
            'competitionDetail.rounds.athleteCategory',
            'registrations',
        ]);

        return view('pages.events.show', compact('event'));
    }
}
