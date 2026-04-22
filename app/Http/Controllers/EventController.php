<?php

namespace App\Http\Controllers;

use App\Enums\EventTypeEnum;
use App\Enums\RoleEnum;
use App\Models\Event;
use App\Models\Setting;
use Awcodes\Mason\Support\MasonRenderer;
use Illuminate\View\View;

class EventController extends Controller
{
    public function index(): View
    {
        $defaultTeamId = Setting::get('default_team_id');

        $events = Event::query()
            ->where('team_id', $defaultTeamId)
            ->where('is_published', true)
            ->with(['eventCategory', 'team', 'organization', 'media'])
            ->latest('date')
            ->paginate(12);

        return view('pages.events.index', compact('events'));
    }

    public function show(Event $event): View
    {
        abort_unless($event->is_published || auth()->user()?->hasAnyAppRole([RoleEnum::SUPER_ADMIN, RoleEnum::ADMIN, RoleEnum::EDITOR]), 404);

        $event->load([
            'eventCategory',
            'team',
            'organization',
            'competitionDetail.disciplines',
            'competitionDetail.athleteCategories',
            'competitionDetail.timetableEntries.competitionRound.athleteCategory',
            'competitionDetail.timetableEntries.currentBattle',
            'competitionDetail.registrationFees.athleteCategory',
            'competitionDetail.rounds.parts.results.user',
            'competitionDetail.rounds.athleteCategory',
            'competitionDetail.rounds.battles',
            'competitionDetail.judges.media',
            'competitionDetail.disciplines.media',
            'registrations',
            'media',
        ]);

        $renderedContent = $this->renderMasonContent($event, 'content');

        $renderedReportContent = '';
        if ($event->event_type === EventTypeEnum::Competition && $event->status === 'finished') {
            $renderedReportContent = $this->renderMasonContent($event, 'report_content');
        }

        $moreEvents = Event::query()
            ->where('id', '!=', $event->id)
            ->where('event_type', $event->event_type)
            ->where('is_published', true)
            ->with(['eventCategory', 'media'])
            ->latest('date')
            ->limit(3)
            ->get();

        $view = match ($event->event_type) {
            EventTypeEnum::Competition => 'pages.events.show-competition',
            EventTypeEnum::Organized => 'pages.events.show-organized',
            default => 'pages.events.show',
        };

        return view($view, compact('event', 'renderedContent', 'renderedReportContent', 'moreEvents'));
    }

    private function renderMasonContent(Event $event, string $field = 'content'): string
    {
        $content = $event->getTranslation($field, app()->getLocale()) ?: [];
        if (is_array($content) && ! empty($content)) {
            return MasonRenderer::make($content)
                ->bricks(PageController::BRICKS)
                ->toUnsafeHtml();
        }

        return '';
    }
}
