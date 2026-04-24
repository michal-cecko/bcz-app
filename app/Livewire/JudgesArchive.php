<?php

namespace App\Livewire;

use App\Models\Judge;
use App\Models\Setting;
use App\Models\SportCategory;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class JudgesArchive extends Component
{
    use WithPagination;

    #[Url(as: 'kategoria')]
    public string $categoryFilter = '';

    #[Url(as: 'hladat')]
    public string $search = '';

    public function updatedCategoryFilter(): void
    {
        $this->resetPage();
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function resetFilters(): void
    {
        $this->categoryFilter = '';
        $this->search = '';
        $this->resetPage();
    }

    public function hasActiveFilters(): bool
    {
        return $this->categoryFilter !== '' || $this->search !== '';
    }

    public function render(): View
    {
        $teamId = Setting::get('default_team_id');

        $query = Judge::query()
            ->with(['certifications', 'judgedCompetitionDetails', 'media']);

        if ($this->categoryFilter) {
            $query->whereHas(
                'judgedCompetitionDetails.disciplines',
                fn ($q) => $q->where('sport_category_id', $this->categoryFilter),
            );
        }

        if ($this->search) {
            $searchTerm = $this->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('name', 'ilike', "%{$searchTerm}%")
                    ->orWhere('slug', 'ilike', "%{$searchTerm}%");
            });
        }

        $judges = $query->orderBy('name')->paginate(12);

        $categories = SportCategory::query()
            ->where('team_id', $teamId)
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        return view('livewire.judges-archive', [
            'judges' => $judges,
            'categories' => $categories,
        ]);
    }
}
