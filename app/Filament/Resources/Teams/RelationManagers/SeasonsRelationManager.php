<?php

namespace App\Filament\Resources\Teams\RelationManagers;

use App\Filament\Resources\TeamSeasons\TeamSeasonResource;
use App\Models\TeamSeason;
use App\Services\SeasonService;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class SeasonsRelationManager extends RelationManager
{
    protected static string $relationship = 'seasons';

    protected static ?string $title = 'Sezóny';

    protected static ?string $modelLabel = 'sezónu';

    protected static ?string $pluralModelLabel = 'Sezóny';

    /**
     * By default Filament treats this relation manager as read-only whenever it
     * renders on a Resource's `ViewRecord` page, which denies `CreateAction` and
     * `DeleteAction` outright (see `RelationManager::isReadOnly()` /
     * `getDefaultActionAuthorizationResponse()`). That made "Sezóny" actions
     * visible on `EditTeam` but missing on `ViewTeam` for the exact same team —
     * see https://github.com/michal-cecko/bcz-app/issues/32.
     *
     * Base read-only-ness on whether the acting user can actually manage this
     * team (`TeamPolicy::update`) instead of on the page type, mirroring
     * `CoachesRelationManager::isReadOnly()`, so the actions show up wherever the
     * team is genuinely editable for them (View or Edit).
     */
    public function isReadOnly(): bool
    {
        return ! auth()->user()?->can('update', $this->getOwnerRecord());
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Názov')
                    ->required(),
                DatePicker::make('starts_at')
                    ->label('Začiatok')
                    ->required(),
                DatePicker::make('ends_at')
                    ->label('Koniec')
                    ->required()
                    ->after('starts_at'),
                TextInput::make('fee_amount')
                    ->label('Suma')
                    ->numeric()
                    ->required()
                    ->minValue(0),
                Select::make('fee_currency')
                    ->label('Mena')
                    ->options(['EUR' => 'EUR', 'CZK' => 'CZK', 'USD' => 'USD'])
                    ->default('EUR')
                    ->required(),
                TextInput::make('max_capacity')
                    ->label('Maximálny počet členov')
                    ->numeric()
                    ->nullable(),
                TextInput::make('payment_deadline_days')
                    ->label('Splatnosť (dní)')
                    ->numeric()
                    ->default(14)
                    ->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Názov')
                    ->searchable()
                    ->url(fn (TeamSeason $record): string => TeamSeasonResource::getUrl('view', ['record' => $record])),
                TextColumn::make('starts_at')
                    ->label('Začiatok')
                    ->date()
                    ->sortable(),
                TextColumn::make('ends_at')
                    ->label('Koniec')
                    ->date()
                    ->sortable(),
                TextColumn::make('fee_amount')
                    ->label('Suma')
                    ->formatStateUsing(fn (TeamSeason $record): string => number_format((float) $record->fee_amount, 2).' '.$record->fee_currency)
                    ->sortable(),
                TextColumn::make('max_capacity')
                    ->label('Kapacita')
                    ->placeholder('Neobmedzená'),
                TextColumn::make('memberships_count')
                    ->label('Členstiev')
                    ->counts('memberships'),
                TextColumn::make('status_badge')
                    ->label('Stav')
                    ->badge()
                    ->state(function (TeamSeason $record): string {
                        if ($record->isActive()) {
                            return 'Aktívna';
                        }
                        if ($record->isFuture()) {
                            return 'Budúca';
                        }

                        return 'Ukončená';
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'Aktívna' => 'success',
                        'Budúca' => 'info',
                        default => 'gray',
                    }),
            ])
            ->defaultSort('starts_at', 'desc')
            ->headerActions([
                CreateAction::make()
                    ->using(function (array $data, RelationManager $livewire): TeamSeason {
                        $team = $livewire->getOwnerRecord();
                        $seasonService = app(SeasonService::class);

                        return $seasonService->createSeasonWithMemberships($team, $data);
                    }),
            ])
            ->recordActions([
                DeleteAction::make()
                    ->requiresConfirmation(),
            ]);
    }
}
