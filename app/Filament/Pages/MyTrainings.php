<?php

namespace App\Filament\Pages;

use App\Enums\GenderEnum;
use App\Enums\RegistrationStatusEnum;
use App\Models\City;
use App\Models\SportCategory;
use App\Models\Training;
use App\Models\TrainingRegistration;
use App\Models\TrainingSchedule;
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
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Str;
use Livewire\Attributes\Url;

class MyTrainings extends Page implements HasTable
{
    use InteractsWithTable;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedAcademicCap;

    protected static ?string $navigationLabel = 'Tréningy';

    protected static ?string $title = 'Tréningy';

    protected static ?int $navigationSort = 1;

    protected string $view = 'filament.pages.my-trainings';

    #[Url]
    public string $categoryFilter = '';

    #[Url]
    public string $dayFilter = '';

    #[Url]
    public string $cityFilter = '';

    #[Url]
    public string $genderFilter = '';

    #[Url]
    public string $search = '';

    public bool $onlyAvailable = true;

    #[Url]
    public int $availablePage = 1;

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

    public function table(Table $table): Table
    {
        $team = Filament::getTenant();

        return $table
            ->query(
                TrainingRegistration::query()
                    ->where('user_id', auth()->id())
                    ->whereNotIn('status', [RegistrationStatusEnum::Cancelled->value])
                    ->whereHas('training', fn (Builder $q) => $q
                        ->where('team_id', $team?->id)
                        ->current()
                    )
                    ->with(['training.sportCategory', 'training.coaches', 'training.city', 'training.season'])
            )
            ->columns([
                TextColumn::make('training.title')
                    ->label('Trening')
                    ->formatStateUsing(fn ($record): string => $record->training->getTranslation('title', app()->getLocale()) ?: $record->training->getTranslation('title', 'sk'))
                    ->description(fn ($record): ?string => $record->training->sportCategory?->getTranslation('name', app()->getLocale()))
                    ->searchable(query: fn (Builder $query, string $search) => $query->whereHas('training', fn ($q) => $q->where('title', 'ilike', "%{$search}%"))),
                TextColumn::make('training.id')
                    ->label('Rozvrh')
                    ->formatStateUsing(function ($record): string {
                        return $record->training->schedules
                            ->map(fn ($s) => ucfirst(mb_substr($s->day, 0, 2)).' '.($s->start_time ? Str::substr($s->start_time, 0, 5) : ''))
                            ->join(', ') ?: ($record->training->start_time ?? '-');
                    }),
                TextColumn::make('training.coaches')
                    ->label('Trener')
                    ->formatStateUsing(fn ($record): string => $record->training->coaches->pluck('name')->implode(', '))
                    ->placeholder('-'),
                TextColumn::make('training.city.name')
                    ->label('Mesto')
                    ->formatStateUsing(fn ($record): ?string => $record->training->city?->getTranslation('name', app()->getLocale()) ?: $record->training->city?->getTranslation('name', 'sk'))
                    ->placeholder('-'),
                TextColumn::make('training.max_capacity')
                    ->label('Kapacita')
                    ->formatStateUsing(function ($record): string {
                        $training = $record->training;
                        if (! $training->max_capacity) {
                            return '-';
                        }
                        $approved = $training->registrations()->where('status', RegistrationStatusEnum::Approved)->count();

                        return "{$approved}/{$training->max_capacity}";
                    }),
                TextColumn::make('status')
                    ->label('Stav')
                    ->badge(),
            ])
            ->defaultSort('created_at', 'desc')
            ->emptyStateHeading('Nie ste registrovany na ziadne treningy')
            ->emptyStateDescription('Pozrite si dostupne treningy nizsie a zaregistrujte sa.')
            ->paginated(false);
    }

    public function historyTable(Schema $schema): Schema
    {
        $team = Filament::getTenant();
        $locale = app()->getLocale();

        $historyRegistrations = TrainingRegistration::query()
            ->where('user_id', auth()->id())
            ->whereHas('training', fn (Builder $q) => $q->where('team_id', $team?->id))
            ->where(function (Builder $q) {
                $q->where('status', RegistrationStatusEnum::Cancelled)
                    ->orWhereHas('training', fn ($tq) => $tq->archived());
            })
            ->with(['training.sportCategory', 'training.season', 'training.city'])
            ->orderByDesc('created_at')
            ->get()
            ->groupBy(fn ($reg) => $reg->training->season?->name ?? 'Bez sezony');

        if ($historyRegistrations->isEmpty()) {
            return $schema->components([
                Placeholder::make('no_history')
                    ->content('Zatial ziadna historia treningov.')
                    ->hiddenLabel(),
            ]);
        }

        $components = [];

        foreach ($historyRegistrations as $seasonName => $registrations) {
            $season = $registrations->first()->training->season;
            $dateRange = $season
                ? $season->starts_at->format('d.m.Y').' - '.$season->ends_at->format('d.m.Y')
                : '';

            $components[] = Section::make($seasonName.($dateRange ? " ({$dateRange})" : ''))
                ->schema(
                    $registrations->map(fn ($reg) => Placeholder::make('reg_'.$reg->id)
                        ->label($reg->training->getTranslation('title', $locale) ?: $reg->training->getTranslation('title', 'sk'))
                        ->content(fn () => $reg->status->getLabel().($reg->cancellation_reason ? ' - '.$reg->cancellation_reason : ''))
                    )->toArray()
                )
                ->collapsed();
        }

        return $schema->components($components);
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

    public function updatedCategoryFilter(): void
    {
        $this->availablePage = 1;
    }

    public function updatedDayFilter(): void
    {
        $this->availablePage = 1;
    }

    public function updatedCityFilter(): void
    {
        $this->availablePage = 1;
    }

    public function updatedGenderFilter(): void
    {
        $this->availablePage = 1;
    }

    public function updatedSearch(): void
    {
        $this->availablePage = 1;
    }

    public function updatedOnlyAvailable(): void
    {
        $this->availablePage = 1;
    }

    private function buildFilters($team, string $locale): Grid
    {
        $categories = SportCategory::query()
            ->whereHas('trainings', fn ($q) => $q->where('team_id', $team?->id)->where('is_active', true))
            ->orderBy('name')
            ->get();

        $days = TrainingSchedule::query()
            ->whereHas('training', fn ($q) => $q->where('is_active', true)->where('team_id', $team?->id))
            ->distinct()
            ->pluck('day')
            ->sort()
            ->values();

        $cities = City::query()
            ->whereHas('trainings', fn ($q) => $q->where('team_id', $team?->id)->where('is_active', true))
            ->orderBy('sort_order')
            ->get();

        return Grid::make(['default' => 2, 'lg' => 6])->schema([
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
            Select::make('cityFilter')
                ->label('Mesto')
                ->options(
                    $cities->mapWithKeys(fn ($city) => [
                        $city->id => $city->getTranslation('name', $locale) ?: $city->getTranslation('name', 'sk'),
                    ])
                )
                ->placeholder('Všetky mestá')
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

    private function getFilteredTrainings(): LengthAwarePaginator
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
            ->current()
            ->whereNotIn('id', $registeredTrainingIds)
            ->with(['sportCategory', 'coaches', 'registrations', 'city'])
            ->orderBy('sort_order');

        if ($this->categoryFilter) {
            $query->where('sport_category_id', $this->categoryFilter);
        }

        if ($this->dayFilter) {
            $query->whereHas('schedules', fn ($q) => $q->where('day', $this->dayFilter));
        }

        if ($this->cityFilter) {
            $query->where('city_id', $this->cityFilter);
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

        $perPage = 12;
        $page = $this->availablePage;

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
