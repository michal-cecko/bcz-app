<?php

namespace App\Models;

use App\Enums\PairingStrategyEnum;
use App\Enums\RegistrationStatusEnum;
use App\Enums\RoundAdvancementTypeEnum;
use App\Enums\ScoringFormatEnum;
use App\Models\Concerns\HasUuidV7;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Collection;

class CompetitionRound extends Model
{
    use HasFactory, HasUuidV7;

    protected $fillable = [
        'competition_detail_id',
        'previous_round_id',
        'athlete_category_id',
        'round_number',
        'name',
        'scoring_format',
        'advancement_type',
        'competitor_count',
        'team_size',
        'pairing_strategy',
        'sort_order',
        'scores_published',
        'competitor_order',
    ];

    protected function casts(): array
    {
        return [
            'scoring_format' => ScoringFormatEnum::class,
            'advancement_type' => RoundAdvancementTypeEnum::class,
            'pairing_strategy' => PairingStrategyEnum::class,
            'round_number' => 'integer',
            'competitor_count' => 'integer',
            'team_size' => 'integer',
            'sort_order' => 'integer',
            'scores_published' => 'boolean',
            'competitor_order' => 'array',
        ];
    }

    public function competitionDetail(): BelongsTo
    {
        return $this->belongsTo(CompetitionDetail::class);
    }

    public function athleteCategory(): BelongsTo
    {
        return $this->belongsTo(AthleteCategory::class);
    }

    public function previousRound(): BelongsTo
    {
        return $this->belongsTo(self::class, 'previous_round_id');
    }

    public function nextRound(): HasOne
    {
        return $this->hasOne(self::class, 'previous_round_id');
    }

    public function parts(): HasMany
    {
        return $this->hasMany(RoundPart::class)->orderBy('sort_order');
    }

    public function battles(): HasMany
    {
        return $this->hasMany(Battle::class)->orderBy('bracket_position');
    }

    public function isQualification(): bool
    {
        return $this->advancement_type === RoundAdvancementTypeEnum::TOP_BY_POINTS;
    }

    public function isBattle(): bool
    {
        return $this->advancement_type === RoundAdvancementTypeEnum::BATTLE_WINNER;
    }

    /**
     * @return Collection<int, EventRegistration>
     */
    public function getOrderedCompetitors(): Collection
    {
        if (! $this->athlete_category_id) {
            return collect();
        }

        $competitors = EventRegistration::query()
            ->where('event_id', $this->competitionDetail->event_id)
            ->where('athlete_category_id', $this->athlete_category_id)
            ->where('status', RegistrationStatusEnum::Approved)
            ->with('user')
            ->orderBy('registered_at')
            ->get();

        if (! empty($this->competitor_order)) {
            $order = collect($this->competitor_order)->flip();

            return $competitors->sortBy(fn (EventRegistration $reg): int => $order->get($reg->user_id, 9999))->values();
        }

        return $competitors;
    }

    /**
     * User IDs advancing into this round from the immediately preceding round in the same
     * category, or null when there is no preceding round (an open qualification field).
     *
     * A battle round advances its winners; a score round advances the top `competitor_count`
     * competitors by total score.
     *
     * @param  Collection<int, CompetitionRound>  $categoryRounds
     * @return Collection<int, string>|null
     */
    public function advancingCompetitorIds(Collection $categoryRounds): ?Collection
    {
        $previous = $categoryRounds
            ->filter(fn (self $round): bool => $round->sort_order < $this->sort_order)
            ->sortByDesc('sort_order')
            ->first();

        if (! $previous) {
            return null;
        }

        if ($previous->isBattle()) {
            $winnerIds = $previous->battles
                ->flatMap(fn (Battle $battle): Collection => $battle->getWinners())
                ->pluck('user_id')
                ->unique()
                ->values();

            // Battles not decided yet → don't restrict (show the provisional field).
            return $winnerIds->isEmpty() ? null : $winnerIds;
        }

        if (! $this->competitor_count) {
            return null;
        }

        $totals = [];
        foreach ($previous->parts as $part) {
            foreach ($part->results as $result) {
                $totals[$result->user_id] = ($totals[$result->user_id] ?? 0) + (float) $result->score;
            }
        }

        // Preceding round not scored yet → don't restrict (show the provisional field).
        if ($totals === []) {
            return null;
        }

        arsort($totals);

        return collect(array_keys($totals))->take($this->competitor_count)->values();
    }

    /**
     * The competitors that actually belong to this round: the qualifiers who advanced from the
     * previous round (battle winners / top-by-score), ordered by competitor_order. Falls back to
     * the full ordered field when there is no previous round or advancement isn't decided yet.
     *
     * @return Collection<int, EventRegistration>
     */
    public function getAdvancedCompetitors(): Collection
    {
        $competitors = $this->getOrderedCompetitors();

        $categoryRounds = self::query()
            ->where('competition_detail_id', $this->competition_detail_id)
            ->where('athlete_category_id', $this->athlete_category_id)
            ->with(['parts.results', 'battles'])
            ->get();

        $advancedIds = $this->advancingCompetitorIds($categoryRounds);

        if ($advancedIds === null) {
            return $competitors;
        }

        return $competitors
            ->filter(fn (EventRegistration $reg): bool => $advancedIds->contains($reg->user_id))
            ->values();
    }

    /**
     * Get total score for a user across all parts of this round.
     */
    public function getTotalScoreForUser(string $userId): ?float
    {
        $total = CompetitionResult::query()
            ->whereIn('round_part_id', $this->parts()->pluck('id'))
            ->where('user_id', $userId)
            ->sum('score');

        return $total > 0 ? (float) $total : null;
    }
}
