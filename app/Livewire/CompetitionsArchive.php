<?php

namespace App\Livewire;

use App\Enums\EventTypeEnum;
use App\Models\Event;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class CompetitionsArchive extends Component
{
    use WithPagination;

    #[Url(as: 'stav')]
    public string $statusFilter = '';

    public function updatedStatusFilter(): void
    {
        $this->resetPage();
    }

    public function render(): View
    {
        $query = Event::query()
            ->where('event_type', EventTypeEnum::Competition)
            ->where('is_published', true)
            ->with(['eventCategory', 'team', 'competitionDetail.disciplines'])
            ->latest('date');

        $all = $query->get();
        $upcoming = $all->filter(fn (Event $e) => $e->status !== 'finished');

        if ($this->statusFilter === 'upcoming') {
            $competitions = $upcoming->values();
            $paginatedCompetitions = null;
        } elseif ($this->statusFilter === 'finished') {
            $paginatedCompetitions = Event::query()
                ->where('event_type', EventTypeEnum::Competition)
                ->where('is_published', true)
                ->where('date', '<', now())
                ->with(['eventCategory', 'team', 'competitionDetail.disciplines'])
                ->latest('date')
                ->paginate(12);

            $competitions = collect();
        } else {
            $competitions = $upcoming;
            $paginatedCompetitions = Event::query()
                ->where('event_type', EventTypeEnum::Competition)
                ->where('is_published', true)
                ->where('date', '<', now())
                ->with(['eventCategory', 'team', 'competitionDetail.disciplines'])
                ->latest('date')
                ->paginate(12);
        }

        return view('livewire.competitions-archive', [
            'upcoming' => $this->statusFilter === 'finished' ? collect() : $upcoming,
            'finished' => $paginatedCompetitions,
            'statusFilter' => $this->statusFilter,
        ]);
    }
}
