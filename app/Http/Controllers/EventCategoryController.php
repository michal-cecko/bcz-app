<?php

namespace App\Http\Controllers;

use App\Models\EventCategory;

class EventCategoryController extends Controller
{
    public function show(EventCategory $eventCategory): \Illuminate\View\View
    {
        $eventCategory->load(['events' => function ($query): void {
            $query->where('is_published', true)
                ->latest('date');
        }]);

        return view('event-categories.show', compact('eventCategory'));
    }
}
