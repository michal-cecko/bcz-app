<?php

namespace App\Services;

use App\Enums\PairingStrategyEnum;
use App\Enums\RegistrationStatusEnum;
use App\Enums\RoundAdvancementTypeEnum;
use App\Exceptions\BattleGenerationException;
use App\Models\Battle;
use App\Models\BattleCompetitor;
use App\Models\CompetitionResult;
use App\Models\CompetitionRound;
use App\Models\EventRegistration;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class BattleGeneratorService
{
    /**
     * Generate battles for a battle round.
     *
     * @return Collection<int, Battle>
     */
    public function generate(
        CompetitionRound $round,
        ?PairingStrategyEnum $strategy = null,
        bool $overwrite = false,
    ): Collection {
        if (! $round->isBattle()) {
            throw BattleGenerationException::invalidAdvancementType();
        }

        if ($this->shouldIncludeThirdPlace($round)) {
            return $this->generateWithThirdPlace($round, $overwrite);
        }

        $teamSize = max(1, (int) $round->team_size);
        $slotsPerBattle = $teamSize * 2;
        $competitorCount = (int) $round->competitor_count;

        if ($competitorCount <= 0) {
            $derived = $this->deriveCompetitorCountFromPrevious($round);
            if ($derived > 0) {
                $round->update(['competitor_count' => $derived]);
                $round->refresh();
                $competitorCount = $derived;
            }
        }

        if ($competitorCount <= 0) {
            throw BattleGenerationException::missingCompetitorCount();
        }

        if ($competitorCount % $slotsPerBattle !== 0) {
            throw BattleGenerationException::invalidCompetitorCount($competitorCount, $teamSize);
        }

        $existing = $round->battles()->count();
        if ($existing > 0 && ! $overwrite) {
            throw BattleGenerationException::alreadyExists($existing);
        }

        $competitors = $this->getCompetitorsForRound($round);
        if ($competitors->count() < $competitorCount) {
            throw BattleGenerationException::insufficientCompetitors($competitors->count(), $competitorCount);
        }

        $competitors = $competitors->values()->take($competitorCount);

        $strategy ??= $round->pairing_strategy ?? PairingStrategyEnum::RANDOM;

        $pairings = $strategy === PairingStrategyEnum::SEEDED
            ? $this->applySeeded($competitors, $teamSize)
            : $this->applyRandom($competitors, $teamSize);

        return DB::transaction(function () use ($round, $pairings): Collection {
            $round->battles()->delete();

            $created = collect();
            foreach ($pairings as $index => $pairing) {
                $battle = Battle::create([
                    'competition_round_id' => $round->id,
                    'athlete_category_id' => $round->athlete_category_id,
                    'bracket_position' => $index + 1,
                ]);

                $this->attachPairing($battle, $pairing);
                $created->push($battle);
            }

            return $created;
        });
    }

    /**
     * Generate Finále + 3rd place battles from the previous battle round.
     * Expects previous to be a battle round with its winner_side set on each battle.
     *
     * @return Collection<int, Battle>
     */
    private function generateWithThirdPlace(CompetitionRound $round, bool $overwrite): Collection
    {
        $previous = $round->previousRound;
        if ($previous === null || ! $previous->isBattle()) {
            throw BattleGenerationException::thirdPlaceRequiresBattleSource();
        }

        $prevBattles = $previous->battles()->with(['sideA.user', 'sideB.user'])->orderBy('bracket_position')->get();
        if ($prevBattles->count() < 2) {
            throw BattleGenerationException::thirdPlaceRequiresTwoSources();
        }

        $winners = collect();
        $losers = collect();
        foreach ($prevBattles as $battle) {
            if ($battle->winner_side === null) {
                throw BattleGenerationException::thirdPlaceNeedsCompleteWinners($battle->bracket_position);
            }
            $winners = $winners->concat($battle->getWinners()->pluck('user')->filter());

            $losingSide = $battle->winner_side === 'a' ? 'sideB' : 'sideA';
            $losers = $losers->concat($battle->{$losingSide}->pluck('user')->filter());
        }

        $teamSize = max(1, (int) $round->team_size);
        $slotsPerBattle = $teamSize * 2;
        if ($winners->count() < $slotsPerBattle || $losers->count() < $slotsPerBattle) {
            throw BattleGenerationException::insufficientCompetitors(
                min($winners->count(), $losers->count()),
                $slotsPerBattle,
            );
        }

        $expectedCount = 2 * $slotsPerBattle;
        if ((int) $round->competitor_count !== $expectedCount) {
            $round->update(['competitor_count' => $expectedCount]);
            $round->refresh();
        }

        $existing = $round->battles()->count();
        if ($existing > 0 && ! $overwrite) {
            throw BattleGenerationException::alreadyExists($existing);
        }

        $finalPair = [
            'a' => $winners->slice(0, $teamSize)->values()->all(),
            'b' => $winners->slice($teamSize, $teamSize)->values()->all(),
        ];
        $thirdPair = [
            'a' => $losers->slice(0, $teamSize)->values()->all(),
            'b' => $losers->slice($teamSize, $teamSize)->values()->all(),
        ];

        return DB::transaction(function () use ($round, $finalPair, $thirdPair): Collection {
            $round->battles()->delete();

            $created = collect();
            foreach ([$finalPair, $thirdPair] as $index => $pairing) {
                $battle = Battle::create([
                    'competition_round_id' => $round->id,
                    'athlete_category_id' => $round->athlete_category_id,
                    'bracket_position' => $index + 1,
                ]);
                $this->attachPairing($battle, $pairing);
                $created->push($battle);
            }

            return $created;
        });
    }

    /**
     * @param  array{a: array<int, User>, b: array<int, User>}  $pairing
     */
    private function attachPairing(Battle $battle, array $pairing): void
    {
        foreach (['a', 'b'] as $side) {
            foreach ($pairing[$side] as $position => $user) {
                BattleCompetitor::create([
                    'battle_id' => $battle->id,
                    'side' => $side,
                    'user_id' => $user->id,
                    'user_name' => $user->name,
                    'position' => $position,
                ]);
            }
        }
    }

    /**
     * Resolve competitors for the round, ordered by seeding rank (best first).
     *
     * @return Collection<int, User>
     */
    public function getCompetitorsForRound(CompetitionRound $round): Collection
    {
        $previous = $round->previousRound;

        if ($previous === null) {
            return $this->getRegisteredCompetitors($round);
        }

        if ($previous->advancement_type === RoundAdvancementTypeEnum::TOP_BY_POINTS) {
            return $this->getQualificationAdvancers($previous, (int) $round->competitor_count);
        }

        $winners = $this->getBattleWinners($previous);

        if ($this->shouldIncludeThirdPlace($round)) {
            return $winners->concat($this->getBattleLosers($previous))->values();
        }

        return $winners;
    }

    /**
     * A battle round automatically includes a 3rd-place battle when its previous
     * round is a battle round with exactly 2 battles (the semifinals → final case).
     */
    private function shouldIncludeThirdPlace(CompetitionRound $round): bool
    {
        $previous = $round->previousRound;

        return $previous !== null
            && $previous->isBattle()
            && $previous->battles()->count() === 2;
    }

    /**
     * @return Collection<int, User>
     */
    private function getBattleLosers(CompetitionRound $round): Collection
    {
        return $round->battles()
            ->with(['sideA.user', 'sideB.user'])
            ->orderBy('bracket_position')
            ->get()
            ->flatMap(function (Battle $b) {
                if ($b->winner_side === null) {
                    return collect();
                }
                $losingSide = $b->winner_side === 'a' ? 'sideB' : 'sideA';

                return $b->{$losingSide}->pluck('user');
            })
            ->filter()
            ->values();
    }

    /**
     * This battle round's competitors no longer match what the previous round would produce.
     *
     * Called from the battle round's own perspective — used inside its scoring modal
     * to surface a "stale" warning banner with a regenerate CTA.
     */
    public function isBattleRoundStale(CompetitionRound $round): bool
    {
        if (! $round->isBattle() || ! $round->battles()->exists()) {
            return false;
        }

        $previous = $round->previousRound;
        if ($previous === null) {
            return false;
        }

        return $this->isDownstreamBattleStale($previous);
    }

    /**
     * Battles in the next round no longer reflect who should advance from this round.
     *
     * Used to prompt the admin to regenerate when they edit scores/winners upstream.
     */
    public function isDownstreamBattleStale(CompetitionRound $round): bool
    {
        $next = $round->nextRound;
        if ($next === null || ! $next->isBattle() || ! $next->battles()->exists()) {
            return false;
        }

        try {
            $expected = $this->getCompetitorsForRound($next)
                ->pluck('id')
                ->filter()
                ->unique()
                ->sort()
                ->values();
        } catch (\Throwable) {
            return false;
        }

        $actual = $next->battles()
            ->with('competitors')
            ->get()
            ->flatMap(fn (Battle $b) => $b->competitors->pluck('user_id'))
            ->filter()
            ->unique()
            ->sort()
            ->values();

        if ($expected->isEmpty() && $actual->isEmpty()) {
            return false;
        }

        return $expected->count() !== $actual->count()
            || $expected->diff($actual)->isNotEmpty();
    }

    /**
     * Previous round is complete enough for auto-generation to run.
     */
    public function isPreviousRoundComplete(CompetitionRound $round): bool
    {
        $previous = $round->previousRound;
        if ($previous === null) {
            return false;
        }

        if ($previous->advancement_type === RoundAdvancementTypeEnum::TOP_BY_POINTS) {
            return $this->isQualificationComplete($previous);
        }

        return $this->isBattleRoundComplete($previous);
    }

    /**
     * @return Collection<int, User>
     */
    private function getRegisteredCompetitors(CompetitionRound $round): Collection
    {
        if ($round->athlete_category_id === null) {
            return collect();
        }

        return EventRegistration::query()
            ->where('event_id', $round->competitionDetail->event_id)
            ->where('athlete_category_id', $round->athlete_category_id)
            ->where('status', RegistrationStatusEnum::Approved)
            ->with('user')
            ->orderBy('registered_at')
            ->get()
            ->map(fn (EventRegistration $reg) => $reg->user)
            ->filter()
            ->values();
    }

    /**
     * @return Collection<int, User>
     */
    private function getQualificationAdvancers(CompetitionRound $round, int $topN): Collection
    {
        $partIds = $round->parts()->pluck('id');
        if ($partIds->isEmpty()) {
            return collect();
        }

        $totals = CompetitionResult::query()
            ->whereIn('round_part_id', $partIds)
            ->selectRaw('user_id, SUM(score) AS total_score')
            ->groupBy('user_id')
            ->orderByDesc('total_score')
            ->limit($topN)
            ->get();

        $userIds = $totals->pluck('user_id');

        $users = User::whereIn('id', $userIds)->get()->keyBy('id');

        return $totals
            ->map(fn ($row) => $users->get($row->user_id))
            ->filter()
            ->values();
    }

    /**
     * @return Collection<int, User>
     */
    private function getBattleWinners(CompetitionRound $round): Collection
    {
        return $round->battles()
            ->with(['sideA.user', 'sideB.user'])
            ->orderBy('bracket_position')
            ->get()
            ->flatMap(fn (Battle $b) => $b->getWinners()->pluck('user'))
            ->filter()
            ->values();
    }

    private function deriveCompetitorCountFromPrevious(CompetitionRound $round): int
    {
        $previous = $round->previousRound;
        if ($previous === null || ! $previous->isBattle()) {
            return 0;
        }

        $prevTeamSize = max(1, (int) $previous->team_size);
        $base = $previous->battles()->count() * $prevTeamSize;

        return $this->shouldIncludeThirdPlace($round) ? $base * 2 : $base;
    }

    private function isQualificationComplete(CompetitionRound $round): bool
    {
        if ($round->athlete_category_id === null) {
            return false;
        }

        $partIds = $round->parts()->pluck('id');
        if ($partIds->isEmpty()) {
            return false;
        }

        $expectedUserIds = EventRegistration::query()
            ->where('event_id', $round->competitionDetail->event_id)
            ->where('athlete_category_id', $round->athlete_category_id)
            ->where('status', RegistrationStatusEnum::Approved)
            ->pluck('user_id');

        if ($expectedUserIds->isEmpty()) {
            return false;
        }

        foreach ($expectedUserIds as $userId) {
            $scoredPartCount = CompetitionResult::query()
                ->whereIn('round_part_id', $partIds)
                ->where('user_id', $userId)
                ->whereNotNull('score')
                ->count();

            if ($scoredPartCount < $partIds->count()) {
                return false;
            }
        }

        return true;
    }

    private function isBattleRoundComplete(CompetitionRound $round): bool
    {
        $battles = $round->battles()->get();
        if ($battles->isEmpty()) {
            return false;
        }

        return $battles->every(fn (Battle $b) => $b->winner_side !== null);
    }

    /**
     * @param  Collection<int, User>  $users
     * @return array<int, array{a: array<int, User>, b: array<int, User>}>
     */
    private function applyRandom(Collection $users, int $teamSize): array
    {
        $shuffled = $users->shuffle()->values();
        $slotsPerBattle = $teamSize * 2;
        $battles = [];

        for ($i = 0; $i < $shuffled->count(); $i += $slotsPerBattle) {
            $chunk = $shuffled->slice($i, $slotsPerBattle)->values();
            $battles[] = [
                'a' => $chunk->slice(0, $teamSize)->values()->all(),
                'b' => $chunk->slice($teamSize, $teamSize)->values()->all(),
            ];
        }

        return $battles;
    }

    /**
     * Seeded pairing: top seed vs bottom seed.
     *
     * For team_size=1, 4 competitors ranked [s0,s1,s2,s3]:
     *   Battle 0: A=[s0], B=[s3]
     *   Battle 1: A=[s1], B=[s2]
     *
     * For team_size=2, 8 competitors:
     *   Battle 0: A=[s0,s1], B=[s6,s7]
     *   Battle 1: A=[s2,s3], B=[s4,s5]
     *
     * @param  Collection<int, User>  $users
     * @return array<int, array{a: array<int, User>, b: array<int, User>}>
     */
    private function applySeeded(Collection $users, int $teamSize): array
    {
        $seeds = $users->values();
        $total = $seeds->count();
        $slotsPerBattle = $teamSize * 2;
        $numBattles = intdiv($total, $slotsPerBattle);
        $battles = [];

        for ($i = 0; $i < $numBattles; $i++) {
            $sideAStart = $i * $teamSize;
            $sideBStart = $total - ($i + 1) * $teamSize;

            $battles[] = [
                'a' => $seeds->slice($sideAStart, $teamSize)->values()->all(),
                'b' => $seeds->slice($sideBStart, $teamSize)->values()->all(),
            ];
        }

        return $battles;
    }
}
