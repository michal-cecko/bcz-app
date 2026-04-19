<?php

namespace App\Livewire;

use App\Enums\EventTypeEnum;
use App\Models\Event;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class CompetitionsArchive extends Component
{
    public function render(): View
    {
        $upcoming = Event::query()
            ->where('event_type', EventTypeEnum::Competition)
            ->where('is_published', true)
            ->where('date', '>=', now())
            ->with(['eventCategory', 'team', 'competitionDetail.disciplines', 'media'])
            ->orderBy('date')
            ->get();

        return view('livewire.competitions-archive', [
            'upcoming' => $upcoming,
        ]);
    }
}
