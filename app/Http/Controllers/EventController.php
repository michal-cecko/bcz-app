<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Setting;

class EventController extends Controller
{
    public function index(): \Illuminate\View\View
    {
        $defaultTeamId = Setting::get('default_team_id');

        $events = Event::query()
            ->where('team_id', $defaultTeamId)
            ->where('is_published', true)
            ->with('eventCategory')
            ->latest('date')
            ->paginate(12);

        return view('events.index', compact('events'));
    }

    public function show(Event $event): \Illuminate\View\View
    {
        abort_unless($event->is_published, 404);

        $event->load('eventCategory', 'team');

        return view('events.show', compact('event'));
    }
}
