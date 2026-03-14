<?php

namespace App\Livewire;

use App\Models\Setting;
use App\Models\SportCategory;
use App\Models\Training;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class TrainingsArchive extends Component
{
    use WithPagination;

    #[Url(as: 'kategoria')]
    public string $categoryFilter = '';

    #[Url(as: 'den')]
    public string $dayFilter = '';

    #[Url(as: 'miesto')]
    public string $locationFilter = '';

    #[Url(as: 'hladat')]
    public string $search = '';

    public function updatedCategoryFilter(): void
    {
        $this->resetPage();
    }

    public function updatedDayFilter(): void
    {
        $this->resetPage();
    }

    public function updatedLocationFilter(): void
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
        $this->dayFilter = '';
        $this->locationFilter = '';
        $this->search = '';
        $this->resetPage();
    }

    public function hasActiveFilters(): bool
    {
        return $this->categoryFilter !== '' || $this->dayFilter !== '' || $this->locationFilter !== '' || $this->search !== '';
    }

    public function render(): View
    {
        $locale = app()->getLocale();
        $teamId = Setting::get('default_team_id');

        $query = Training::query()
            ->where('is_active', true)
            ->where('team_id', $teamId)
            ->with(['sportCategory', 'coaches', 'team'])
            ->withCount('registrations')
            ->orderBy('sort_order');

        if ($this->categoryFilter) {
            $query->where('sport_category_id', $this->categoryFilter);
        }

        if ($this->dayFilter) {
            $query->whereJsonContains('schedule_days', $this->dayFilter);
        }

        if ($this->locationFilter) {
            $query->where("place_name->{$locale}", $this->locationFilter);
        }

        if ($this->search) {
            $searchTerm = $this->search;
            $query->where(function ($q) use ($searchTerm, $locale) {
                $q->where("title->{$locale}", 'ilike', "%{$searchTerm}%")
                    ->orWhere("place_name->{$locale}", 'ilike', "%{$searchTerm}%");
            });
        }

        $trainings = $query->paginate(12);

        $categories = SportCategory::query()
            ->where('team_id', $teamId)
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        $days = Training::query()
            ->where('is_active', true)
            ->where('team_id', $teamId)
            ->whereNotNull('schedule_days')
            ->pluck('schedule_days')
            ->flatten()
            ->unique()
            ->sort()
            ->values();

        $locations = Training::query()
            ->where('is_active', true)
            ->where('team_id', $teamId)
            ->whereNotNull("place_name->{$locale}")
            ->pluck('place_name')
            ->map(fn ($val) => is_array($val) ? ($val[$locale] ?? null) : $val)
            ->filter()
            ->unique()
            ->sort()
            ->values();

        return view('livewire.trainings-archive', [
            'trainings' => $trainings,
            'categories' => $categories,
            'days' => $days,
            'locations' => $locations,
        ]);
    }
}
