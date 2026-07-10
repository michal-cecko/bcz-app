<?php

namespace App\Filament\Resources\Events\Concerns;

use App\Enums\PairingStrategyEnum;
use App\Exceptions\BattleGenerationException;
use App\Models\Battle;
use App\Models\BattleCompetitor;
use App\Models\BattlePartScore;
use App\Models\CompetitionResult;
use App\Models\CompetitionRound;
use App\Models\RoundPart;
use App\Services\BattleGeneratorService;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\View;
use Filament\Support\Icons\Heroicon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\On;

trait HasScoringActions
{
    abstract protected function getRoundFromRecord(mixed $record): ?CompetitionRound;

    protected function makePublishAllScoresAction(string $name = 'publishAllScores'): Action
    {
        return Action::make($name)
            ->label('Zverejniť výsledky')
            ->icon(Heroicon::Eye)
            ->color('success')
            ->requiresConfirmation()
            ->modalHeading('Zverejniť body za všetky kolá')
            ->modalDescription('Body za všetky kolá budú zverejnené.')
            ->action(fn () => $this->getScoredRounds()->each(fn (CompetitionRound $r) => $r->update(['scores_published' => true])))
            ->visible(fn (): bool => ($scored = $this->getScoredRounds())->isNotEmpty() && $scored->contains('scores_published', false));
    }

    protected function makeHideAllScoresAction(string $name = 'hideAllScores'): Action
    {
        return Action::make($name)
            ->label('Skryť výsledky')
            ->icon(Heroicon::EyeSlash)
            ->color('gray')
            ->requiresConfirmation()
            ->modalHeading('Skryť body za všetky kolá')
            ->modalDescription('Body za všetky kolá budú skryté.')
            ->action(fn () => $this->getScoredRounds()->each(fn (CompetitionRound $r) => $r->update(['scores_published' => false])))
            ->visible(fn (): bool => ($scored = $this->getScoredRounds())->isNotEmpty() && $scored->every('scores_published', true));
    }

    /**
     * @return Collection<int, CompetitionRound>
     */
    protected function getScoredRounds(): Collection
    {
        $detail = $this->getOwnerRecord()->competitionDetail;
        if (! $detail) {
            return new Collection;
        }

        return $detail->rounds()
            ->get()
            ->filter(function (CompetitionRound $round): bool {
                $hasQualificationScores = CompetitionResult::query()
                    ->whereIn('round_part_id', $round->parts()->pluck('id'))
                    ->exists();

                $hasBattleScores = $round->battles()
                    ->where(fn ($q) => $q
                        ->whereNotNull('part_winners')
                        ->orWhereHas('partScores', fn ($q2) => $q2->whereNotNull('score')))
                    ->exists();

                return $hasQualificationScores || $hasBattleScores;
            });
    }

    protected function makeCompetitorOrderAction(string $name = 'competitorOrder'): Action
    {
        return Action::make($name)
            ->label(fn (mixed $record): string => $this->getRoundFromRecord($record)?->isBattle() ? 'Pavúk' : 'Zoradiť')
            ->icon(Heroicon::Bars3BottomLeft)
            ->color('gray')
            ->modalHeading(fn (mixed $record): string => $this->getRoundFromRecord($record)?->isBattle()
                ? 'Pavúk — '.$this->getRoundFromRecord($record)?->name
                : 'Poradie súťažiacich — '.$this->getRoundFromRecord($record)?->name)
            ->modalWidth(fn (mixed $record): string => $this->getRoundFromRecord($record)?->isBattle() ? '3xl' : 'md')
            ->modalSubmitAction(false)
            ->modalCancelActionLabel('Zavrieť')
            ->extraModalFooterActions(fn (mixed $record): array => $this->getRoundFromRecord($record)?->isBattle()
                ? [$this->makeGenerateBattlesAction($this->getRoundFromRecord($record))]
                : [])
            ->schema(fn (mixed $record): array => $this->buildOrderView($this->getRoundFromRecord($record)));
    }

    protected function makeScoringAction(string $name = 'scoring'): Action
    {
        return Action::make($name)
            ->label('Bodovanie')
            ->icon(Heroicon::Trophy)
            ->color('warning')
            ->modalHeading(fn (mixed $record): string => 'Bodovanie — '.$this->getRoundFromRecord($record)?->name)
            ->modalWidth('5xl')
            ->modalSubmitAction(false)
            ->modalCancelActionLabel('Zavrieť')
            ->schema(fn (mixed $record): array => $this->buildScoringView($this->getRoundFromRecord($record)));
    }

    protected function makeGenerateBattlesAction(CompetitionRound $round): Action
    {
        return Action::make('generateBattles')
            ->label(__('battle.generation.action_label'))
            ->icon(Heroicon::Sparkles)
            ->color('primary')
            ->schema([
                Select::make('pairing_strategy')
                    ->label(__('battle.generation.pairing_strategy_label'))
                    ->options(PairingStrategyEnum::class)
                    ->default(($round->pairing_strategy ?? PairingStrategyEnum::RANDOM)->value)
                    ->required(),
            ])
            ->requiresConfirmation()
            ->modalHeading(__('battle.generation.modal_heading'))
            ->modalDescription(function () use ($round): string {
                $count = $round->battles()->count();

                return $count > 0
                    ? __('battle.generation.modal_description_overwrite', ['count' => $count])
                    : __('battle.generation.modal_description_new');
            })
            ->modalSubmitActionLabel(__('battle.generation.submit_label'))
            ->action(function (array $data, Action $action) use ($round): void {
                try {
                    $strategy = $data['pairing_strategy'] instanceof PairingStrategyEnum
                        ? $data['pairing_strategy']
                        : PairingStrategyEnum::from($data['pairing_strategy']);

                    $created = app(BattleGeneratorService::class)->generate(
                        $round,
                        $strategy,
                        overwrite: true,
                    );

                    Notification::make()
                        ->success()
                        ->title(__('battle.generation.success_title'))
                        ->body(__('battle.generation.success_body', [
                            'count' => $created->count(),
                            'round' => $round->name,
                        ]))
                        ->send();

                    $this->dispatch('round-battles-refreshed',
                        roundId: $round->id,
                        scoring: $this->buildBattleScoringPayload($round),
                        bracket: $this->buildBracketPayload($round),
                    );
                } catch (BattleGenerationException $e) {
                    Notification::make()
                        ->danger()
                        ->title(__('battle.generation.failed_title'))
                        ->body($e->getMessage())
                        ->send();

                    $action->halt();
                }
            });
    }

    protected function makePublishScoresAction(string $name = 'publishScores'): Action
    {
        return Action::make($name)
            ->label(fn (mixed $record): string => $this->getRoundFromRecord($record)?->scores_published
                ? 'Skryť body'
                : 'Zverejniť body')
            ->icon(fn (mixed $record): Heroicon => $this->getRoundFromRecord($record)?->scores_published
                ? Heroicon::EyeSlash
                : Heroicon::Eye)
            ->color(fn (mixed $record): string => $this->getRoundFromRecord($record)?->scores_published
                ? 'gray'
                : 'success')
            ->requiresConfirmation()
            ->action(function (mixed $record): void {
                $round = $this->getRoundFromRecord($record);
                $round?->update(['scores_published' => ! $round->scores_published]);
            });
    }

    // ── View builder ───────────────────────────────────────────────

    protected function buildScoringView(?CompetitionRound $round): array
    {
        if (! $round) {
            return [];
        }

        $round->load(['parts', 'battles', 'competitionDetail']);

        $isQualification = $round->isQualification();
        $isBattle = $round->isBattle();

        $next = $round->nextRound;

        $viewData = [
            'roundId' => $round->id,
            'roundType' => $isQualification ? 'qualification' : ($isBattle ? 'battle' : 'unknown'),
            'scoringFormat' => $round->scoring_format?->value ?? 'points',
            'scoresPublished' => (bool) $round->scores_published,
            'competitors' => [],
            'parts' => [],
            'scores' => (object) [],
            'battles' => [],
            'advanceCount' => $next?->competitor_count !== null ? (int) $next->competitor_count : null,
            'nextRoundName' => $next?->name,
        ];

        if ($isQualification) {
            $competitors = $round->getAdvancedCompetitors();
            $viewData['competitors'] = $competitors->map(fn ($reg, $i) => [
                'id' => $reg->user_id,
                'name' => $reg->user?->name ?? 'Neznámy',
                'order' => $i,
            ])->values()->toArray();

            $viewData['parts'] = $round->parts->map(fn ($part) => [
                'id' => $part->id,
                'name' => $part->getTranslation('name', 'sk'),
            ])->toArray();

            $scores = [];
            foreach ($round->parts as $part) {
                $partScores = [];
                $results = CompetitionResult::query()
                    ->where('round_part_id', $part->id)
                    ->get();
                foreach ($results as $result) {
                    $partScores[$result->user_id] = (float) $result->score;
                }
                $scores[$part->id] = (object) $partScores;
            }
            $viewData['scores'] = (object) $scores;
        }

        if ($isBattle) {
            $round->load(['battles.sideA', 'battles.sideB', 'battles.partScores']);
            $viewData['scoringFormat'] = $round->scoring_format?->value ?? 'points';
            $viewData['parts'] = $round->parts->map(fn ($part) => [
                'id' => $part->id,
                'name' => $part->getTranslation('name', 'sk'),
            ])->toArray();
            $viewData['battles'] = $round->battles->map(fn (Battle $battle) => $this->battleToPayload($battle))->toArray();

            $viewData['isStale'] = app(BattleGeneratorService::class)->isBattleRoundStale($round);
            $viewData['previousRoundName'] = $round->previousRound?->name;
        }

        return [
            View::make('filament.scoring-table')
                ->viewData($viewData),
        ];
    }

    // ── Livewire persistence methods ───────────────────────────────

    // ── Ordering & bracket setup ───────────────────────────────────

    protected function buildOrderView(?CompetitionRound $round): array
    {
        if (! $round) {
            return [];
        }

        if ($round->isBattle()) {
            return $this->buildBracketView($round);
        }

        return $this->buildCompetitorOrderView($round);
    }

    protected function buildCompetitorOrderView(CompetitionRound $round): array
    {
        $competitors = $round->getAdvancedCompetitors()->map(fn ($reg) => [
            'id' => $reg->user_id,
            'name' => $reg->user?->name ?? 'Neznámy',
        ])->values()->toArray();

        return [
            View::make('filament.competitor-order')
                ->viewData([
                    'roundId' => $round->id,
                    'competitors' => $competitors,
                ]),
        ];
    }

    protected function buildBracketView(CompetitionRound $round): array
    {
        $round->load(['battles.sideA', 'battles.sideB', 'competitionDetail']);

        $competitors = $round->getOrderedCompetitors()->map(fn ($reg) => [
            'id' => $reg->user_id,
            'name' => $reg->user?->name ?? 'Neznámy',
        ])->values()->toArray();

        $battles = $round->battles->map(fn (Battle $battle) => [
            'id' => $battle->id,
            'bracket' => $battle->bracket_position,
            'sideA' => $battle->sideA->pluck('user_id')->values()->all(),
            'sideB' => $battle->sideB->pluck('user_id')->values()->all(),
        ])->toArray();

        return [
            View::make('filament.battle-bracket')
                ->viewData([
                    'roundId' => $round->id,
                    'teamSize' => max(1, (int) $round->team_size),
                    'competitors' => $competitors,
                    'battles' => $battles,
                ]),
        ];
    }

    public function persistCompetitorOrder(string $roundId, array $order): void
    {
        CompetitionRound::where('id', $roundId)->update(['competitor_order' => $order]);
    }

    public function persistBracket(string $roundId, array $battlesData): void
    {
        $round = CompetitionRound::find($roundId);
        if (! $round) {
            return;
        }

        $competitorNames = $round->getOrderedCompetitors()
            ->mapWithKeys(fn ($reg) => [$reg->user_id => $reg->user?->name ?? 'Neznámy']);

        DB::transaction(function () use ($round, $battlesData, $competitorNames): void {
            $existingIds = $round->battles()->pluck('id')->toArray();
            $incomingIds = collect($battlesData)->pluck('id')->filter()->toArray();

            $toDelete = array_diff($existingIds, $incomingIds);
            if ($toDelete) {
                Battle::whereIn('id', $toDelete)->delete();
            }

            foreach ($battlesData as $i => $data) {
                $battleId = $data['id'] ?? null;
                $sideA = array_values(array_filter((array) ($data['sideA'] ?? [])));
                $sideB = array_values(array_filter((array) ($data['sideB'] ?? [])));

                $attrs = [
                    'bracket_position' => $i + 1,
                ];

                if ($battleId) {
                    Battle::where('id', $battleId)->update($attrs);
                    $battle = Battle::find($battleId);
                } else {
                    $battle = Battle::create(array_merge($attrs, [
                        'competition_round_id' => $round->id,
                        'athlete_category_id' => $round->athlete_category_id,
                    ]));
                }

                if (! $battle) {
                    continue;
                }

                $battle->competitors()->delete();

                foreach (['a' => $sideA, 'b' => $sideB] as $side => $userIds) {
                    foreach ($userIds as $position => $userId) {
                        BattleCompetitor::create([
                            'battle_id' => $battle->id,
                            'side' => $side,
                            'user_id' => $userId,
                            'user_name' => $competitorNames->get($userId, 'TBD'),
                            'position' => $position,
                        ]);
                    }
                }
            }
        });
    }

    // ── Score persistence ──────────────────────────────────────────

    public function persistScore(string $partId, string $userId, mixed $score): void
    {
        if ($score === null || $score === '') {
            CompetitionResult::query()
                ->where('round_part_id', $partId)
                ->where('user_id', $userId)
                ->delete();
        } else {
            CompetitionResult::updateOrCreate(
                ['round_part_id' => $partId, 'user_id' => $userId],
                ['score' => $score],
            );
        }

        $roundId = RoundPart::where('id', $partId)->value('competition_round_id');
        if ($roundId) {
            $round = CompetitionRound::find($roundId);
            $this->maybeAutoGenerateNextRound($round);
        }
    }

    public function persistPlace(string $roundId, string $userId, mixed $place): void
    {
        $round = CompetitionRound::find($roundId);
        if (! $round) {
            return;
        }

        $partIds = $round->parts()->pluck('id');
        $placeValue = ($place === null || $place === '') ? null : (int) $place;

        CompetitionResult::query()
            ->whereIn('round_part_id', $partIds)
            ->where('user_id', $userId)
            ->update(['place' => $placeValue]);
    }

    public function persistBattlePartScore(string $battleId, string $partId, string $side, mixed $score): void
    {
        $battle = Battle::find($battleId);
        if (! $battle) {
            return;
        }

        if ($score === null || $score === '') {
            BattlePartScore::query()
                ->where('battle_id', $battleId)
                ->where('round_part_id', $partId)
                ->where('side', $side)
                ->delete();
        } else {
            BattlePartScore::updateOrCreate(
                ['battle_id' => $battleId, 'round_part_id' => $partId, 'side' => $side],
                ['score' => $score],
            );
        }

        $battle->load(['competitionRound', 'partScores']);
        $battle->updateAutoWinner();

        $this->maybeAutoGenerateNextRound($battle->competitionRound);
    }

    public function persistBattlePartWinner(string $battleId, string $partId, ?string $side): void
    {
        $battle = Battle::find($battleId);
        if (! $battle) {
            return;
        }

        $partWinners = $battle->part_winners ?? [];
        if ($side === null || $side === '') {
            unset($partWinners[$partId]);
        } else {
            $partWinners[$partId] = $side;
        }

        $battle->update(['part_winners' => $partWinners]);

        $battle->load('competitionRound');
        $battle->updateAutoWinner();

        $this->maybeAutoGenerateNextRound($battle->competitionRound);
    }

    public function persistScoresPublished(string $roundId, bool $published): void
    {
        CompetitionRound::where('id', $roundId)->update(['scores_published' => $published]);
    }

    // ── Auto-generation ────────────────────────────────────────────

    protected function maybeAutoGenerateNextRound(?CompetitionRound $completedRound): void
    {
        if (! $completedRound) {
            return;
        }

        $next = $completedRound->nextRound;
        if (! $next) {
            return;
        }

        if (! $next->isBattle()) {
            return;
        }

        if ($next->battles()->exists()) {
            return;
        }

        $service = app(BattleGeneratorService::class);
        if (! $service->isPreviousRoundComplete($next)) {
            return;
        }

        try {
            $strategy = $next->pairing_strategy instanceof PairingStrategyEnum
                ? $next->pairing_strategy
                : PairingStrategyEnum::RANDOM;

            $created = $service->generate($next, $strategy);

            Notification::make()
                ->success()
                ->title('Battle vygenerované')
                ->body("Kolo „{$next->name}\" automaticky dostalo {$created->count()} battle.")
                ->send();
        } catch (\Throwable $e) {
            // Silently skip — the admin can regenerate manually from the round row.
            // Manual generation surfaces errors via the button's own notification.
        }
    }

    /**
     * Livewire event handler invoked from the inline regenerate button inside the Bodovanie modal banner.
     */
    #[On('regenerateNextRound')]
    public function regenerateNextRound(string $roundId): void
    {
        $round = CompetitionRound::find($roundId);
        if (! $round || ! $round->isBattle()) {
            return;
        }

        try {
            $strategy = $round->pairing_strategy instanceof PairingStrategyEnum
                ? $round->pairing_strategy
                : PairingStrategyEnum::RANDOM;

            $created = app(BattleGeneratorService::class)->generate(
                $round,
                $strategy,
                overwrite: true,
            );

            Notification::make()
                ->success()
                ->title(__('battle.generation.success_title'))
                ->body(__('battle.generation.regenerate_success_body', [
                    'count' => $created->count(),
                    'round' => $round->name,
                ]))
                ->send();

            $this->dispatch('round-battles-refreshed',
                roundId: $round->id,
                scoring: $this->buildBattleScoringPayload($round),
                bracket: $this->buildBracketPayload($round),
            );
        } catch (BattleGenerationException $e) {
            Notification::make()
                ->danger()
                ->title(__('battle.generation.regenerate_failed_title'))
                ->body($e->getMessage())
                ->send();
        }
    }

    /**
     * Payload for the Bodovanie modal Alpine state.
     *
     * @return array<string, mixed>
     */
    protected function buildBattleScoringPayload(CompetitionRound $round): array
    {
        $round->load(['battles.sideA', 'battles.sideB', 'battles.partScores']);

        return [
            'battles' => $round->battles->map(fn (Battle $battle) => $this->battleToPayload($battle))->values()->toArray(),
            'isStale' => app(BattleGeneratorService::class)->isBattleRoundStale($round),
        ];
    }

    /**
     * Shape of a single battle for the Bodovanie Alpine state.
     *
     * @return array<string, mixed>
     */
    protected function battleToPayload(Battle $battle): array
    {
        $partScoresA = $battle->partScores
            ->where('side', 'a')
            ->mapWithKeys(fn (BattlePartScore $s) => [$s->round_part_id => (float) $s->score])
            ->toArray();

        $partScoresB = $battle->partScores
            ->where('side', 'b')
            ->mapWithKeys(fn (BattlePartScore $s) => [$s->round_part_id => (float) $s->score])
            ->toArray();

        return [
            'id' => $battle->id,
            'bracket' => $battle->bracket_position,
            'nameA' => $battle->getCompetitorALabel(),
            'nameB' => $battle->getCompetitorBLabel(),
            'partScoresA' => (object) $partScoresA,
            'partScoresB' => (object) $partScoresB,
            'totalA' => $battle->side_a_score,
            'totalB' => $battle->side_b_score,
            'winnerSide' => $battle->winner_side,
            'partWinners' => $battle->part_winners ?? (object) [],
        ];
    }

    /**
     * Payload for the Pavúk (bracket editor) Alpine state.
     *
     * @return array<string, mixed>
     */
    protected function buildBracketPayload(CompetitionRound $round): array
    {
        $round->load(['battles.sideA', 'battles.sideB']);

        return [
            'battles' => $round->battles->map(fn (Battle $battle) => [
                'id' => $battle->id,
                'bracket' => $battle->bracket_position,
                'sideA' => $battle->sideA->pluck('user_id')->values()->all(),
                'sideB' => $battle->sideB->pluck('user_id')->values()->all(),
            ])->values()->toArray(),
        ];
    }
}
