<?php

namespace App\Livewire;

use App\Models\Team;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class TeamsArchive extends Component
{
    use WithPagination;

    #[Url(as: 'hladat')]
    public string $search = '';

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function resetFilters(): void
    {
        $this->search = '';
        $this->resetPage();
    }

    public function hasActiveFilters(): bool
    {
        return $this->search !== '';
    }

    public function render(): View
    {
        $locale = app()->getLocale();

        $query = Team::query()
            ->where('is_active', true)
            ->withCount('members', 'trainings');

        if ($this->search) {
            $searchTerm = $this->search;
            $query->where(function ($q) use ($searchTerm, $locale) {
                $q->where("name->{$locale}", 'ilike', "%{$searchTerm}%")
                    ->orWhere('slug', 'ilike', "%{$searchTerm}%");
            });
        }

        $teams = $query->orderBy('name->sk')->paginate(12);

        return view('livewire.teams-archive', [
            'teams' => $teams,
        ]);
    }
}
