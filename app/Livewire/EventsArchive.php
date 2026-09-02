<?php

namespace App\Livewire;

use App\Models\Event;
use App\Models\EventCategory;
use App\Models\Setting;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Str;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class EventsArchive extends Component
{
    use WithPagination;

    #[Url(as: 'kategoria')]
    public string $categoryFilter = '';

    #[Url(as: 'typ')]
    public string $typeFilter = '';

    public function updatedCategoryFilter(): void
    {
        $this->resetPage();
    }

    public function updatedTypeFilter(): void
    {
        $this->resetPage();
    }

    public function render(): View
    {
        $teamId = Setting::get('default_team_id');

        // event_category_id is a native Postgres uuid column. A malformed value here
        // (e.g. from a scanner/bot poking the `kategoria` query param) would otherwise
        // reach the DB as a bound parameter and blow up with an uncaught SQLSTATE[22P02]
        // instead of just being treated as "no filter".
        if ($this->categoryFilter !== '' && ! Str::isUuid($this->categoryFilter)) {
            $this->categoryFilter = '';
        }

        $query = Event::query()
            ->where('is_published', true)
            ->where('team_id', $teamId)
            ->with(['eventCategory', 'team', 'organization', 'media'])
            ->latest('date');

        if ($this->categoryFilter) {
            $query->where('event_category_id', $this->categoryFilter);
        }

        if ($this->typeFilter) {
            $query->where('event_type', $this->typeFilter);
        }

        $events = $query->paginate(12);

        $eventCategories = EventCategory::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        return view('livewire.events-archive', [
            'events' => $events,
            'eventCategories' => $eventCategories,
        ]);
    }
}
