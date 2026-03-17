<?php

namespace App\Filament\Pages;

use App\Enums\GenderEnum;
use App\Enums\RegistrationStatusEnum;
use App\Models\SportCategory;
use App\Models\Training;
use App\Services\TrainingFilterService;
use Filament\Facades\Filament;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Pages\Page;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\View;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Pagination\LengthAwarePaginator;
use Livewire\Attributes\Url;
use Livewire\WithPagination;

class AvailableTrainings extends Page
{
    use WithPagination;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedMagnifyingGlass;

    protected static ?string $navigationLabel = 'Dostupné tréningy';

    protected static ?string $title = 'Dostupné tréningy';

    protected static ?int $navigationSort = 2;

    #[Url]
    public string $categoryFilter = '';

    #[Url]
    public string $dayFilter = '';

    #[Url]
    public string $locationFilter = '';

    #[Url]
    public string $genderFilter = '';

    #[Url]
    public string $search = '';

    public bool $onlyAvailable = true;

    public function mount(): void
    {
        $user = auth()->user();

        if ($user?->gender && ! request()->has('genderFilter')) {
            $this->genderFilter = $user->gender->value;
        }
    }

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()?->isMemberLevel() ?? false;
    }

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

    public function updatedGenderFilter(): void
    {
        $this->resetPage();
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedOnlyAvailable(): void
    {
        $this->resetPage();
    }

    public function content(Schema $schema): Schema
    {
        $team = Filament::getTenant();
        $locale = app()->getLocale();
        $paginated = $this->getFilteredTrainings();
        $trainings = $paginated->getCollection();

        $components = [
            $this->buildFilters($team, $locale),
        ];

        if ($trainings->isEmpty()) {
            $components[] = Section::make()
                ->schema([
                    Placeholder::make('empty')
                        ->content('Momentálne nie sú dostupné žiadne tréningy.')
                        ->hiddenLabel(),
                ]);
        } else {
            $components[] = Grid::make(['default' => 1, 'sm' => 2, 'lg' => 3])
                ->schema(
                    $trainings->map(fn (Training $training) => $this->buildTrainingCard($training))->toArray()
                );

            $components[] = Placeholder::make('pagination')
                ->hiddenLabel()
                ->content(fn () => $paginated->links());
        }

        return $schema->components($components);
    }

    private function buildFilters($team, string $locale): Grid
    {
        $categories = SportCategory::query()
            ->whereHas('trainings', fn ($q) => $q->where('team_id', $team?->id)->where('is_active', true))
            ->orderBy('name')
            ->get();

        $days = Training::query()
            ->where('is_active', true)
            ->where('team_id', $team?->id)
            ->whereNotNull('schedule_days')
            ->pluck('schedule_days')
            ->flatten()
            ->unique()
            ->sort()
            ->values();

        $locations = Training::query()
            ->where('is_active', true)
            ->where('team_id', $team?->id)
            ->whereNotNull("place_name->{$locale}")
            ->pluck('place_name')
            ->map(fn ($val) => is_array($val) ? ($val[$locale] ?? null) : $val)
            ->filter()
            ->unique()
            ->sort()
            ->values();

        return Grid::make(['default' => 2, 'lg' => 3])->schema([
            TextInput::make('search')
                ->label(__('archive.search'))
                ->placeholder(__('archive.search_placeholder'))
                ->prefixIcon('heroicon-m-magnifying-glass')
                ->live(debounce: 300),
            Select::make('categoryFilter')
                ->label(__('archive.category'))
                ->options(
                    $categories->mapWithKeys(fn ($category) => [
                        $category->id => $category->getTranslation('name', $locale) ?: $category->getTranslation('name', 'sk'),
                    ])
                )
                ->placeholder(__('archive.all_categories'))
                ->live(),
            Select::make('dayFilter')
                ->label(__('archive.day'))
                ->options($days->mapWithKeys(fn ($day) => [$day => __('archive.days.'.$day)]))
                ->placeholder(__('archive.all_days'))
                ->live(),
            Select::make('locationFilter')
                ->label(__('archive.location'))
                ->options($locations->mapWithKeys(fn ($loc) => [$loc => $loc]))
                ->placeholder(__('archive.all_locations'))
                ->live(),
            Select::make('genderFilter')
                ->label(__('archive.gender'))
                ->options(collect(GenderEnum::cases())->mapWithKeys(fn ($g) => [$g->value => $g->getLabel()]))
                ->placeholder(__('archive.all_genders'))
                ->live(),
            Toggle::make('onlyAvailable')
                ->label('Len dostupné')
                ->inline(false)
                ->default(true)
                ->live(),
        ]);
    }

    private function buildTrainingCard(Training $training): Section
    {
        return Section::make()
            ->schema([
                View::make('filament.components.training-card')
                    ->viewData(['training' => $training]),
            ]);
    }

    private function getFilteredTrainings()
    {
        $team = Filament::getTenant();
        $user = auth()->user();
        $locale = app()->getLocale();
        $filterService = app(TrainingFilterService::class);

        $registeredTrainingIds = $user
            ? $user->trainingRegistrations()
                ->whereIn('status', [RegistrationStatusEnum::Approved, RegistrationStatusEnum::Pending])
                ->pluck('training_id')
                ->toArray()
            : [];

        $query = Training::query()
            ->where('team_id', $team?->id)
            ->where('is_active', true)
            ->whereNotIn('id', $registeredTrainingIds)
            ->with(['sportCategory', 'coaches', 'registrations'])
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

        // "Only available" = matches user age + not full capacity
        // When off, show all trainings (no age/capacity filtering)
        $perPage = 12;
        $page = $this->getPage();

        $allResults = $query->get()
            ->when($this->onlyAvailable, fn ($collection) => $collection
                ->filter(fn (Training $training) => $user ? $filterService->matchesAge($training, $user) : true)
                ->filter(fn (Training $training) => ! $training->isFull())
            )
            ->values();

        return new LengthAwarePaginator(
            $allResults->forPage($page, $perPage),
            $allResults->count(),
            $perPage,
            $page,
            ['path' => request()->url(), 'query' => request()->query()]
        );
    }
}
