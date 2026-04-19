<?php

namespace App\Models;

use App\Enums\ScoringFormatEnum;
use App\Models\Concerns\HasUuidV7;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

class Battle extends Model
{
    use HasFactory, HasUuidV7;

    protected $fillable = [
        'competition_round_id',
        'athlete_category_id',
        'bracket_position',
        'winner_side',
        'part_winners',
    ];

    protected function casts(): array
    {
        return [
            'bracket_position' => 'integer',
            'part_winners' => 'json',
        ];
    }

    public function competitionRound(): BelongsTo
    {
        return $this->belongsTo(CompetitionRound::class);
    }

    public function athleteCategory(): BelongsTo
    {
        return $this->belongsTo(AthleteCategory::class);
    }

    public function competitors(): HasMany
    {
        return $this->hasMany(BattleCompetitor::class)->orderBy('position');
    }

    public function sideA(): HasMany
    {
        return $this->hasMany(BattleCompetitor::class)->where('side', 'a')->orderBy('position');
    }

    public function sideB(): HasMany
    {
        return $this->hasMany(BattleCompetitor::class)->where('side', 'b')->orderBy('position');
    }

    public function partScores(): HasMany
    {
        return $this->hasMany(BattlePartScore::class);
    }

    public function getCompetitorALabel(): string
    {
        return $this->buildSideLabel('a');
    }

    public function getCompetitorBLabel(): string
    {
        return $this->buildSideLabel('b');
    }

    private function buildSideLabel(string $side): string
    {
        $relation = $side === 'a' ? 'sideA' : 'sideB';
        $competitors = $this->relationLoaded($relation)
            ? $this->{$relation}
            : $this->competitors()->where('side', $side)->orderBy('position')->get();

        if ($competitors->isEmpty()) {
            return 'TBD';
        }

        return $competitors->pluck('user_name')->implode(' + ');
    }

    /**
     * Sum of part scores for a side. Returns null when that side has no rows
     * (so the UI can distinguish "not scored yet" from "scored zero").
     */
    private function sumPartScores(string $side): ?float
    {
        $scores = $this->relationLoaded('partScores')
            ? $this->partScores->where('side', $side)
            : $this->partScores()->where('side', $side)->get();

        if ($scores->isEmpty()) {
            return null;
        }

        return (float) $scores->sum(fn (BattlePartScore $s) => (float) $s->score);
    }

    /**
     * Count of parts this side won (plus draws, which count for both sides —
     * mirroring the tie-break logic in computeWinnerSide). Null when nothing
     * has been voted on.
     */
    private function countVoteWins(string $side): ?int
    {
        $partWinners = $this->part_winners ?? [];
        if (empty($partWinners)) {
            return null;
        }

        $wins = count(array_filter($partWinners, fn ($w) => $w === $side));
        $draws = count(array_filter($partWinners, fn ($w) => $w === 'draw'));

        return $wins + $draws;
    }

    /**
     * Side's numeric score for display. For POINTS: sum of part scores.
     * For COACH_DECISION: count of parts won (including draws).
     */
    private function sideScore(string $side): ?float
    {
        $format = $this->competitionRound?->scoring_format;

        if ($format === ScoringFormatEnum::POINTS) {
            return $this->sumPartScores($side);
        }

        if ($format === ScoringFormatEnum::COACH_DECISION) {
            $count = $this->countVoteWins($side);

            return $count === null ? null : (float) $count;
        }

        return null;
    }

    public function getSideAScoreAttribute(): ?float
    {
        return $this->sideScore('a');
    }

    public function getSideBScoreAttribute(): ?float
    {
        return $this->sideScore('b');
    }

    /**
     * Auto-determine winner based on scoring format.
     *
     * Returns 'a', 'b', or null (tie/incomplete).
     */
    public function computeWinnerSide(): ?string
    {
        $format = $this->competitionRound?->scoring_format;

        if ($format === ScoringFormatEnum::POINTS) {
            if (! $this->hasCompleteScoring()) {
                return null;
            }

            $a = (float) $this->side_a_score;
            $b = (float) $this->side_b_score;

            if ($a > $b) {
                return 'a';
            }
            if ($b > $a) {
                return 'b';
            }

            return null;
        }

        if ($format === ScoringFormatEnum::COACH_DECISION) {
            $partWinners = $this->part_winners ?? [];
            if (empty($partWinners)) {
                return null;
            }

            $aWins = count(array_filter($partWinners, fn ($w) => $w === 'a'));
            $bWins = count(array_filter($partWinners, fn ($w) => $w === 'b'));
            $draws = count(array_filter($partWinners, fn ($w) => $w === 'draw'));

            $aScore = $aWins + $draws;
            $bScore = $bWins + $draws;

            if ($aScore > $bScore) {
                return 'a';
            }
            if ($bScore > $aScore) {
                return 'b';
            }

            return null;
        }

        return null;
    }

    public function isDraw(): bool
    {
        return $this->computeWinnerSide() === null && $this->hasCompleteScoring();
    }

    public function hasCompleteScoring(): bool
    {
        $format = $this->competitionRound?->scoring_format;

        if ($format === ScoringFormatEnum::POINTS) {
            $partsCount = $this->competitionRound->parts()->count();
            if ($partsCount === 0) {
                return false;
            }

            $scores = $this->relationLoaded('partScores')
                ? $this->partScores
                : $this->partScores()->get();

            $filled = $scores->filter(fn (BattlePartScore $s) => $s->score !== null);
            $aCount = $filled->where('side', 'a')->count();
            $bCount = $filled->where('side', 'b')->count();

            return $aCount === $partsCount && $bCount === $partsCount;
        }

        if ($format === ScoringFormatEnum::COACH_DECISION) {
            $partsCount = $this->competitionRound->parts()->count();
            $entryCount = count($this->part_winners ?? []);

            return $partsCount > 0 && $entryCount >= $partsCount && $entryCount % $partsCount === 0;
        }

        return false;
    }

    public function updateAutoWinner(): void
    {
        $this->update(['winner_side' => $this->computeWinnerSide()]);
    }

    public function hasWinner(): bool
    {
        return $this->winner_side !== null;
    }

    /**
     * Get the winning side's competitors (users).
     *
     * @return Collection<int, BattleCompetitor>
     */
    public function getWinners(): Collection
    {
        if ($this->winner_side === null) {
            return collect();
        }

        $relation = $this->winner_side === 'a' ? 'sideA' : 'sideB';

        return $this->relationLoaded($relation)
            ? $this->{$relation}
            : $this->competitors()->where('side', $this->winner_side)->orderBy('position')->get();
    }
}
