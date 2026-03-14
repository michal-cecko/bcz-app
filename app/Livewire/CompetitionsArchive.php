<?php

namespace App\Livewire;

use App\Models\Competition;
use App\Models\Setting;
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
        $teamId = Setting::get('default_team_id');

        $query = Competition::query()
            ->where('is_published', true)
            ->with(['disciplines', 'organizerTeam'])
            ->latest('date_start');

        $all = $query->get();
        $upcoming = $all->filter(fn (Competition $c) => $c->status !== 'finished');
        $finished = $all->filter(fn (Competition $c) => $c->status === 'finished');

        if ($this->statusFilter === 'upcoming') {
            $competitions = Competition::query()
                ->where('is_published', true)
                ->with(['disciplines', 'organizerTeam'])
                ->latest('date_start')
                ->get()
                ->filter(fn (Competition $c) => $c->status !== 'finished')
                ->values();

            $paginatedCompetitions = null;
        } elseif ($this->statusFilter === 'finished') {
            $paginatedCompetitions = Competition::query()
                ->where('is_published', true)
                ->where('date_start', '<', now())
                ->with(['disciplines', 'organizerTeam'])
                ->latest('date_start')
                ->paginate(12);

            $competitions = collect();
        } else {
            $competitions = $upcoming;
            $paginatedCompetitions = Competition::query()
                ->where('is_published', true)
                ->where('date_start', '<', now())
                ->with(['disciplines', 'organizerTeam'])
                ->latest('date_start')
                ->paginate(12);
        }

        return view('livewire.competitions-archive', [
            'upcoming' => $this->statusFilter === 'finished' ? collect() : $upcoming,
            'finished' => $paginatedCompetitions,
            'statusFilter' => $this->statusFilter,
        ]);
    }
}
