<?php

namespace App\Filament\Resources\Events\RelationManagers;

use App\Enums\EventTypeEnum;
use App\Enums\PairingStrategyEnum;
use App\Enums\RoundAdvancementTypeEnum;
use App\Enums\ScoringFormatEnum;
use App\Filament\Resources\Events\Concerns\HasScoringActions;
use App\Models\CompetitionRound;
use App\Models\Discipline;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Repeater\TableColumn;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class RoundsRelationManager extends RelationManager
{
    use HasScoringActions;

    protected static string $relationship = 'competitionDetail';

    protected static ?string $title = 'Kolá';

    protected static ?string $modelLabel = 'kolo';

    protected static ?string $pluralModelLabel = 'Kolá';

    public static function canViewForRecord(Model $ownerRecord, string $pageClass): bool
    {
        return $ownerRecord->event_type === EventTypeEnum::Competition;
    }

    public function isReadOnly(): bool
    {
        return false;
    }

    protected function getRoundFromRecord(mixed $record): ?CompetitionRound
    {
        return $record instanceof CompetitionRound ? $record : null;
    }

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('name')
                ->label('Názov')
                ->required(),
            TextInput::make('round_number')
                ->label('Číslo kola')
                ->numeric()
                ->required(),
            Select::make('athlete_category_id')
                ->label('Kategória')
                ->relationship('athleteCategory')
                ->getOptionLabelFromRecordUsing(fn (Model $record): string => $record->getTranslation('name', 'sk'))
                ->preload()
                ->searchable()
                ->live(),
            Select::make('scoring_format')
                ->label('Formát hodnotenia')
                ->options(ScoringFormatEnum::class),
            Select::make('advancement_type')
                ->label('Typ postupu')
                ->options(RoundAdvancementTypeEnum::class)
                ->required()
                ->live(),
            TextInput::make('competitor_count')
                ->label('Počet súťažiacich v tomto kole')
                ->numeric()
                ->minValue(1)
                ->rules([
                    function (Get $get) {
                        return function (string $attribute, $value, \Closure $fail) use ($get) {
                            if ($get('advancement_type') !== RoundAdvancementTypeEnum::BATTLE_WINNER->value) {
                                return;
                            }
                            if ($value === null || $value === '') {
                                return;
                            }
                            $teamSize = max(1, (int) ($get('team_size') ?: 1));
                            $slots = $teamSize * 2;
                            if (((int) $value) % $slots !== 0) {
                                $fail("Počet súťažiacich musí byť deliteľný dvojnásobkom veľkosti tímu ({$slots}).");
                            }
                        };
                    },
                ]),
            TextInput::make('team_size')
                ->label('Veľkosť tímu (1 pre 1v1, 2 pre 2v2, …)')
                ->numeric()
                ->minValue(1)
                ->default(1)
                ->visible(fn (Get $get): bool => $get('advancement_type') === RoundAdvancementTypeEnum::BATTLE_WINNER->value),
            Select::make('pairing_strategy')
                ->label('Stratégia párovania')
                ->options(PairingStrategyEnum::class)
                ->default(PairingStrategyEnum::RANDOM->value)
                ->visible(fn (Get $get): bool => $get('advancement_type') === RoundAdvancementTypeEnum::BATTLE_WINNER->value),
            Select::make('previous_round_id')
                ->label('Predchádzajúce kolo')
                ->placeholder('Žiadne — toto je prvé kolo')
                ->options(function (Get $get, ?CompetitionRound $record): array {
                    return CompetitionRound::query()
                        ->where('competition_detail_id', $this->getOwnerRecord()->competitionDetail?->id)
                        ->when($get('athlete_category_id'), fn ($q, $catId) => $q->where('athlete_category_id', $catId))
                        ->when($record?->id, fn ($q, $id) => $q->where('id', '!=', $id))
                        ->orderBy('sort_order')
                        ->orderBy('round_number')
                        ->pluck('name', 'id')
                        ->toArray();
                })
                ->searchable(),
            TextInput::make('sort_order')
                ->label('Poradie')
                ->numeric()
                ->default(0),
            Repeater::make('parts')
                ->label('Časti (disciplíny)')
                ->helperText('Za každú časť sa v bodovaní zobrazí samostatný stĺpec na skóre. Predvyplnené sú všetky disciplíny súťaže.')
                ->relationship()
                ->table([
                    TableColumn::make('Názov'),
                    TableColumn::make('Trvanie (s)'),
                ])
                ->schema([
                    TextInput::make('name.sk')
                        ->label('Názov')
                        ->required(),
                    TextInput::make('duration_seconds')
                        ->label('Trvanie (s)')
                        ->numeric()
                        ->minValue(1),
                ])
                ->default(fn (): array => $this->getDefaultPartsFromDisciplines())
                ->orderColumn('sort_order')
                ->reorderableWithButtons()
                ->addActionLabel('Pridať časť')
                ->columnSpanFull(),
        ]);
    }

    /**
     * Default round parts seeded from the competition's disciplines.
     *
     * @return list<array{name: array{sk: string}, duration_seconds: null}>
     */
    protected function getDefaultPartsFromDisciplines(): array
    {
        return $this->getOwnerRecord()->competitionDetail
            ?->disciplines
            ->sortBy('sort_order')
            ->map(fn (Discipline $discipline): array => [
                'name' => ['sk' => $discipline->getTranslation('name', 'sk')],
                'duration_seconds' => null,
            ])
            ->values()
            ->all() ?? [];
    }

    public function table(Table $table): Table
    {
        $detail = $this->getOwnerRecord()->competitionDetail;

        if (! $detail) {
            return $table->columns([]);
        }

        return $table
            ->relationship(fn () => $detail->rounds())
            ->reorderable('sort_order')
            ->defaultSort('sort_order')
            ->columns([
                TextColumn::make('round_number')
                    ->label('#')
                    ->sortable(),
                TextColumn::make('name')
                    ->label('Názov')
                    ->searchable(),
                TextColumn::make('athleteCategory.name')
                    ->label('Kategória')
                    ->state(fn ($record): ?string => $record->athleteCategory?->getTranslation('name', 'sk'))
                    ->placeholder('-'),
                TextColumn::make('advancement_type')
                    ->label('Postup')
                    ->badge(),
                TextColumn::make('parts_count')
                    ->label('Časti')
                    ->counts('parts'),
                TextColumn::make('battles_count')
                    ->label('Battle')
                    ->counts('battles'),
                IconColumn::make('scores_published')
                    ->label('Body')
                    ->boolean(),
            ])
            ->headerActions([
                $this->makePublishAllScoresAction(),
                $this->makeHideAllScoresAction(),
                CreateAction::make()
                    ->modalHeading('Vytvoriť kolo'),
            ])
            ->recordActions([
                $this->makeScoringAction(),
                $this->makeCompetitorOrderAction(),
                $this->makePublishScoresAction(),
                EditAction::make()
                    ->modalHeading('Upraviť kolo'),
                DeleteAction::make()
                    ->modalHeading('Odstrániť kolo'),
            ]);
    }
}
