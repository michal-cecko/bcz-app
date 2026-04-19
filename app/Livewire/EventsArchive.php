<?php

namespace App\Livewire;

use App\Models\Event;
use App\Models\EventCategory;
use App\Models\Setting;
use Illuminate\Contracts\View\View;
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
