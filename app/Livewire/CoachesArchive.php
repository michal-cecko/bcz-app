<?php

namespace App\Livewire;

use App\Enums\RoleEnum;
use App\Models\Setting;
use App\Models\SportCategory;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class CoachesArchive extends Component
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
        $locale = app()->getLocale();
        $teamId = Setting::get('default_team_id');

        $query = User::whereHas('teams', fn ($q) => $q
            ->where('teams.id', $teamId)
            ->where('team_user.role', RoleEnum::COACH->value)
            ->where('team_user.is_active', true)
        )->with(['coachProfile', 'certifications', 'coachedTrainings' => fn ($q) => $q->where('is_active', true)->with('sportCategory')]);

        if ($this->categoryFilter) {
            $query->whereHas('coachedTrainings', fn ($q) => $q->where('sport_category_id', $this->categoryFilter));
        }

        if ($this->search) {
            $searchTerm = $this->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('name', 'ilike', "%{$searchTerm}%")
                    ->orWhere('slug', 'ilike', "%{$searchTerm}%");
            });
        }

        $coaches = $query->orderBy('name')->paginate(12);

        $categories = SportCategory::query()
            ->where('team_id', $teamId)
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        return view('livewire.coaches-archive', [
            'coaches' => $coaches,
            'categories' => $categories,
        ]);
    }
}
