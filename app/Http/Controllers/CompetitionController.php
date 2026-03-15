<?php

namespace App\Http\Controllers;

use App\Enums\EventTypeEnum;
use App\Models\Event;
use App\Models\Team;
use Illuminate\View\View;

class CompetitionController extends Controller
{
    public function show(Team $team, Event $event): View
    {
        abort_unless($event->is_published, 404);
        abort_unless($event->event_type === EventTypeEnum::Competition, 404);
        abort_unless($event->team_id === $team->id, 404);

        $event->load([
            'team',
            'eventCategory',
            'organization',
            'competitionDetail.disciplines',
            'competitionDetail.athleteCategories',
            'competitionDetail.timetableEntries',
            'competitionDetail.registrationFees.athleteCategory',
            'competitionDetail.rounds.parts',
            'competitionDetail.rounds.athleteCategory',
        ]);

        return view('pages.competitions.show', ['competition' => $event]);
    }
}
