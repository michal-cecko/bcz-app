<?php

namespace App\Livewire;

use App\Enums\GenderEnum;
use App\Enums\RegistrationStatusEnum;
use App\Models\City;
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

    #[Url(as: 'vek')]
    public string $ageGroupFilter = '';

    #[Url(as: 'pohlavie')]
    public string $genderFilter = '';

    #[Url(as: 'mesto')]
    public string $cityFilter = '';

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

    public function updatedAgeGroupFilter(): void
    {
        $this->resetPage();
    }

    public function updatedGenderFilter(): void
    {
        $this->resetPage();
    }

    public function updatedCityFilter(): void
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
        $this->cityFilter = '';
        $this->ageGroupFilter = '';
        $this->genderFilter = '';
        $this->search = '';
        $this->resetPage();
    }

    public function hasActiveFilters(): bool
    {
        return $this->categoryFilter !== '' || $this->dayFilter !== '' || $this->locationFilter !== '' || $this->cityFilter !== '' || $this->ageGroupFilter !== '' || $this->genderFilter !== '' || $this->search !== '';
    }

    public function render(): View
    {
        $locale = app()->getLocale();
        $teamId = Setting::get('default_team_id');

        $query = Training::query()
            ->where('is_active', true)
            ->where('team_id', $teamId)
            ->current()
            ->with(['sportCategory', 'coaches', 'team', 'city', 'registrations' => function ($q) {
                if (auth()->check()) {
                    $q->where('user_id', auth()->id())
                        ->whereNotIn('status', [RegistrationStatusEnum::Cancelled->value])
                        ->with('payments');
                } else {
                    $q->whereRaw('1 = 0');
                }
            }])
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

        if ($this->cityFilter) {
            $query->where('city_id', $this->cityFilter);
        }

        if ($this->ageGroupFilter) {
            $parts = explode('-', $this->ageGroupFilter);
            if (count($parts) === 2) {
                $query->where('min_age', '<=', (int) $parts[1])
                    ->where('max_age', '>=', (int) $parts[0]);
            }
        }

        if ($this->genderFilter) {
            $query->where(fn ($q) => $q->where('gender', $this->genderFilter)->orWhereNull('gender'));
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

        $ageGroups = Training::query()
            ->where('is_active', true)
            ->where('team_id', $teamId)
            ->where(fn ($q) => $q->whereNotNull('min_age')->orWhereNotNull('max_age'))
            ->get()
            ->map(function (Training $t) {
                if ($t->min_age !== null && $t->max_age !== null) {
                    return $t->min_age.'-'.$t->max_age;
                }
                if ($t->min_age !== null) {
                    return $t->min_age.'+';
                }

                return 'do '.$t->max_age;
            })
            ->unique()
            ->sort()
            ->values();

        $cities = City::query()
            ->whereHas('trainings', fn ($q) => $q->where('is_active', true)->where('team_id', $teamId))
            ->orderBy('sort_order')
            ->get();

        return view('livewire.trainings-archive', [
            'trainings' => $trainings,
            'categories' => $categories,
            'days' => $days,
            'locations' => $locations,
            'cities' => $cities,
            'ageGroups' => $ageGroups,
            'genders' => GenderEnum::cases(),
        ]);
    }
}
