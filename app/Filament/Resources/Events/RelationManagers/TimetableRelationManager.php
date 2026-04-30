<?php

namespace App\Filament\Resources\Events\RelationManagers;

use App\Enums\EventTypeEnum;
use App\Enums\TimetableEntryStatusEnum;
use App\Enums\TimetableEntryTypeEnum;
use App\Filament\Resources\Events\Concerns\HasScoringActions;
use App\Models\CompetitionRound;
use App\Models\TimetableEntry;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class TimetableRelationManager extends RelationManager
{
    use HasScoringActions;

    protected static string $relationship = 'competitionDetail';

    protected static ?string $title = 'Harmonogram';

    protected static ?string $modelLabel = 'položka';

    protected static ?string $pluralModelLabel = 'Harmonogram';

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
        return $record instanceof TimetableEntry ? $record->competitionRound : null;
    }

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Tabs::make('Preklady')
                ->tabs([
                    Tabs\Tab::make('SK')
                        ->schema([
                            TextInput::make('title.sk')
                                ->label('Názov (SK)')
                                ->required(),
                            TextInput::make('description.sk')
                                ->label('Popis (SK)'),
                        ]),
                    Tabs\Tab::make('EN')
                        ->schema([
                            TextInput::make('title.en')
                                ->label('Názov (EN)'),
                            TextInput::make('description.en')
                                ->label('Popis (EN)'),
                        ]),
                    Tabs\Tab::make('CZ')
                        ->schema([
                            TextInput::make('title.cs')
                                ->label('Názov (CZ)'),
                            TextInput::make('description.cs')
                                ->label('Popis (CZ)'),
                        ]),
                ])
                ->columnSpanFull(),
            Select::make('type')
                ->label('Typ')
                ->options(TimetableEntryTypeEnum::class)
                ->placeholder('Žiadny'),
            Select::make('competition_round_id')
                ->label('Súťažné kolo')
                ->relationship(
                    name: 'competitionRound',
                    modifyQueryUsing: fn ($query) => $query->where(
                        'competition_detail_id',
                        $this->getOwnerRecord()->competitionDetail?->id,
                    ),
                )
                ->getOptionLabelFromRecordUsing(fn (Model $record): string => "{$record->name} ({$record->athleteCategory?->getTranslation('name', 'sk')})")
                ->placeholder('Žiadne')
                ->preload()
                ->searchable(),
            DateTimePicker::make('scheduled_time')
                ->label('Plánovaný čas')
                ->required()
                ->timezone(fn (): string => $this->getOwnerRecord()->getTimezone()),
            DateTimePicker::make('actual_start_time')
                ->label('Skutočný začiatok')
                ->timezone(fn (): string => $this->getOwnerRecord()->getTimezone()),
            DateTimePicker::make('actual_end_time')
                ->label('Skutočný koniec')
                ->timezone(fn (): string => $this->getOwnerRecord()->getTimezone()),
            Select::make('status')
                ->label('Stav')
                ->options(TimetableEntryStatusEnum::class)
                ->default(TimetableEntryStatusEnum::PENDING)
                ->required(),
            TextInput::make('sort_order')
                ->label('Poradie')
                ->numeric()
                ->default(0),
        ]);
    }

    public function table(Table $table): Table
    {
        $detail = $this->getOwnerRecord()->competitionDetail;

        if (! $detail) {
            return $table->columns([]);
        }

        return $table
            ->relationship(fn () => $detail->timetableEntries())
            ->reorderable('sort_order')
            ->defaultSort('sort_order')
            ->columns([
                TextColumn::make('scheduled_time')
                    ->label('Čas')
                    ->dateTime('H:i')
                    ->timezone($this->getOwnerRecord()->getTimezone())
                    ->sortable(),
                TextColumn::make('title')
                    ->label('Názov')
                    ->searchable(),
                TextColumn::make('type')
                    ->label('Typ')
                    ->badge(),
                TextColumn::make('status')
                    ->label('Stav')
                    ->badge(),
                TextColumn::make('current_performer')
                    ->label('Aktuálne')
                    ->state(fn (TimetableEntry $record): string => $record->getCurrentPerformerLabel() ?? '-')
                    ->placeholder('-'),
                TextColumn::make('actual_start_time')
                    ->label('Začiatok')
                    ->dateTime('H:i')
                    ->timezone($this->getOwnerRecord()->getTimezone())
                    ->placeholder('-'),
                TextColumn::make('actual_end_time')
                    ->label('Koniec')
                    ->dateTime('H:i')
                    ->timezone($this->getOwnerRecord()->getTimezone())
                    ->placeholder('-'),
            ])
            ->headerActions([
                $this->revertAction(),
                $this->advanceAction(),
                ActionGroup::make([
                    $this->prevCompetitorAction(),
                    $this->nextCompetitorAction(),
                ])->buttonGroup()
                    ->visible(fn (): bool => $this->getActiveRoundEntry() !== null),
                $this->makePublishAllScoresAction('timetable_publishAll'),
                $this->makeHideAllScoresAction('timetable_hideAll'),
                ActionGroup::make([
                    $this->jumpToAction(),
                    CreateAction::make()
                        ->modalHeading('Vytvoriť položku harmonogramu'),
                ])->label('Viac')
                    ->icon(Heroicon::EllipsisVertical)
                    ->color('gray'),
            ])
            ->recordActions([
                $this->makeScoringAction('timetable_scoring')
                    ->visible(fn (TimetableEntry $record): bool => $record->competition_round_id !== null),
                $this->makeCompetitorOrderAction('timetable_order')
                    ->visible(fn (TimetableEntry $record): bool => $record->competition_round_id !== null),
                EditAction::make()
                    ->modalHeading('Upraviť položku harmonogramu'),
                DeleteAction::make()
                    ->modalHeading('Odstrániť položku harmonogramu'),
            ]);
    }

    // ── Harmonogram navigation ──────────────────────────────────────

    private function advanceAction(): Action
    {
        return Action::make('advance')
            ->label('Posunúť ďalej')
            ->icon(Heroicon::ChevronRight)
            ->color('success')
            ->requiresConfirmation()
            ->modalHeading('Posunúť harmonogram ďalej')
            ->modalDescription(function (): string {
                $entries = $this->getEntries();
                $current = $entries->firstWhere('status', TimetableEntryStatusEnum::IN_PROGRESS);
                $nextPending = $entries->firstWhere('status', TimetableEntryStatusEnum::PENDING);

                if ($current && $nextPending) {
                    return "Ukončiť \"{$current->getTranslation('title', 'sk')}\" a spustiť \"{$nextPending->getTranslation('title', 'sk')}\"?";
                }

                if (! $current && $nextPending) {
                    return "Spustiť \"{$nextPending->getTranslation('title', 'sk')}\"?";
                }

                if ($current && ! $nextPending) {
                    return "Ukončiť \"{$current->getTranslation('title', 'sk')}\"? Toto je posledná položka.";
                }

                return 'Žiadna ďalšia položka na spustenie.';
            })
            ->action(function (): void {
                $entries = $this->getEntries();
                $current = $entries->firstWhere('status', TimetableEntryStatusEnum::IN_PROGRESS);
                $nextPending = $entries->firstWhere('status', TimetableEntryStatusEnum::PENDING);

                if ($current) {
                    $current->update([
                        'status' => TimetableEntryStatusEnum::FINISHED,
                        'actual_end_time' => now(),
                        'current_competitor_index' => null,
                        'current_battle_id' => null,
                    ]);
                }

                if ($nextPending) {
                    $nextPending->update([
                        'status' => TimetableEntryStatusEnum::IN_PROGRESS,
                        'actual_start_time' => now(),
                    ]);
                    $nextPending->initializeTracking();
                }
            })
            ->visible(function (): bool {
                $entries = $this->getEntries();

                return $entries->contains('status', TimetableEntryStatusEnum::IN_PROGRESS)
                    || $entries->contains('status', TimetableEntryStatusEnum::PENDING);
            });
    }

    private function revertAction(): Action
    {
        return Action::make('revert')
            ->label('Vrátiť späť')
            ->icon(Heroicon::ChevronLeft)
            ->color('warning')
            ->requiresConfirmation()
            ->modalHeading('Vrátiť harmonogram späť')
            ->modalDescription(function (): string {
                $entries = $this->getEntries();
                $current = $entries->firstWhere('status', TimetableEntryStatusEnum::IN_PROGRESS);
                $lastFinished = $entries->where('status', TimetableEntryStatusEnum::FINISHED)->last();

                if ($current && $lastFinished) {
                    return "Vrátiť \"{$current->getTranslation('title', 'sk')}\" na čakanie a obnoviť \"{$lastFinished->getTranslation('title', 'sk')}\"?";
                }

                if (! $current && $lastFinished) {
                    return "Obnoviť \"{$lastFinished->getTranslation('title', 'sk')}\" ako prebiehajúcu?";
                }

                return 'Žiadna položka na vrátenie.';
            })
            ->action(function (): void {
                $entries = $this->getEntries();
                $current = $entries->firstWhere('status', TimetableEntryStatusEnum::IN_PROGRESS);
                $lastFinished = $entries->where('status', TimetableEntryStatusEnum::FINISHED)->last();

                if ($current) {
                    $current->update([
                        'status' => TimetableEntryStatusEnum::PENDING,
                        'actual_start_time' => null,
                        'current_competitor_index' => null,
                        'current_battle_id' => null,
                    ]);
                }

                if ($lastFinished) {
                    $lastFinished->update([
                        'status' => TimetableEntryStatusEnum::IN_PROGRESS,
                        'actual_end_time' => null,
                    ]);
                    $lastFinished->initializeTracking();
                }
            })
            ->visible(function (): bool {
                $entries = $this->getEntries();

                return $entries->contains('status', TimetableEntryStatusEnum::FINISHED);
            });
    }

    private function jumpToAction(): Action
    {
        return Action::make('jumpTo')
            ->label('Posunúť na')
            ->icon(Heroicon::ArrowRight)
            ->color('gray')
            ->modalHeading('Posunúť harmonogram na konkrétnu položku')
            ->modalDescription('Všetky položky pred vybranou budú označené ako dokončené. Vybraná položka sa spustí.')
            ->schema(fn (): array => [
                Select::make('entry_id')
                    ->label('Položka')
                    ->options(
                        $this->getEntries()
                            ->mapWithKeys(fn (TimetableEntry $entry) => [
                                $entry->id => $entry->scheduled_time?->format('H:i').' — '.$entry->getTranslation('title', 'sk'),
                            ])
                    )
                    ->required()
                    ->searchable(),
            ])
            ->action(function (array $data): void {
                $entries = $this->getEntries();
                $targetIndex = $entries->search(fn (TimetableEntry $e) => $e->id === $data['entry_id']);

                if ($targetIndex === false) {
                    return;
                }

                $entries->each(function (TimetableEntry $entry, int $index) use ($entries, $targetIndex): void {
                    if ($index < $targetIndex) {
                        $nextEntry = $entries->get($index + 1);
                        $entry->update([
                            'status' => TimetableEntryStatusEnum::FINISHED,
                            'actual_start_time' => $entry->actual_start_time ?? $entry->scheduled_time,
                            'actual_end_time' => $entry->actual_end_time ?? ($nextEntry?->scheduled_time ?? $entry->scheduled_time),
                            'current_competitor_index' => null,
                            'current_battle_id' => null,
                        ]);
                    } elseif ($index === $targetIndex) {
                        $entry->update([
                            'status' => TimetableEntryStatusEnum::IN_PROGRESS,
                            'actual_start_time' => now(),
                            'actual_end_time' => null,
                            'current_competitor_index' => null,
                            'current_battle_id' => null,
                        ]);
                        $entry->initializeTracking();
                    } else {
                        $entry->update([
                            'status' => TimetableEntryStatusEnum::PENDING,
                            'actual_start_time' => null,
                            'actual_end_time' => null,
                            'current_competitor_index' => null,
                            'current_battle_id' => null,
                        ]);
                    }
                });
            });
    }

    // ── Competitor / Battle navigation ──────────────────────────────

    private function nextCompetitorAction(): Action
    {
        return Action::make('nextCompetitor')
            ->label(fn (): string => $this->getActiveRoundEntry()?->isBattleRound() ? 'Ďalší battle' : 'Ďalší súťažiaci')
            ->icon(Heroicon::Forward)
            ->color('info')
            ->action(function (): void {
                $entry = $this->getActiveRoundEntry();
                if (! $entry) {
                    return;
                }

                if ($entry->isBattleRound()) {
                    $battles = $entry->competitionRound->battles;
                    if ($entry->current_battle_id === null) {
                        $entry->update(['current_battle_id' => $battles->first()?->id]);
                    } else {
                        $currentIndex = $battles->search(fn ($b) => $b->id === $entry->current_battle_id);
                        $next = $battles->get($currentIndex + 1);
                        if ($next) {
                            $entry->update(['current_battle_id' => $next->id]);
                        }
                    }
                } else {
                    $maxIndex = $entry->getOrderedCompetitors()->count() - 1;
                    $current = $entry->current_competitor_index;
                    if ($current === null) {
                        $entry->update(['current_competitor_index' => 0]);
                    } elseif ($current < $maxIndex) {
                        $entry->update(['current_competitor_index' => $current + 1]);
                    }
                }
            })
            ->visible(fn (): bool => $this->getActiveRoundEntry() !== null);
    }

    private function prevCompetitorAction(): Action
    {
        return Action::make('prevCompetitor')
            ->label(fn (): string => $this->getActiveRoundEntry()?->isBattleRound() ? 'Predch. battle' : 'Predch. súťažiaci')
            ->icon(Heroicon::Backward)
            ->color('info')
            ->action(function (): void {
                $entry = $this->getActiveRoundEntry();
                if (! $entry) {
                    return;
                }

                if ($entry->isBattleRound()) {
                    $battles = $entry->competitionRound->battles;
                    if ($entry->current_battle_id !== null) {
                        $currentIndex = $battles->search(fn ($b) => $b->id === $entry->current_battle_id);
                        if ($currentIndex > 0) {
                            $entry->update(['current_battle_id' => $battles->get($currentIndex - 1)->id]);
                        }
                    }
                } else {
                    $current = $entry->current_competitor_index;
                    if ($current !== null && $current > 0) {
                        $entry->update(['current_competitor_index' => $current - 1]);
                    }
                }
            })
            ->visible(function (): bool {
                $entry = $this->getActiveRoundEntry();
                if (! $entry) {
                    return false;
                }

                if ($entry->isBattleRound()) {
                    return $entry->current_battle_id !== null;
                }

                return $entry->current_competitor_index !== null && $entry->current_competitor_index > 0;
            });
    }

    // ── Helpers ─────────────────────────────────────────────────────

    private function getActiveRoundEntry(): ?TimetableEntry
    {
        return $this->getEntries()
            ->first(fn (TimetableEntry $e) => $e->status === TimetableEntryStatusEnum::IN_PROGRESS
                && $e->type === TimetableEntryTypeEnum::COMPETITION_ROUND
                && $e->competition_round_id !== null
            );
    }

    /**
     * @return Collection<int, TimetableEntry>
     */
    private function getEntries(): Collection
    {
        return $this->getOwnerRecord()
            ->competitionDetail
            ->timetableEntries()
            ->orderBy('sort_order')
            ->get();
    }
}
