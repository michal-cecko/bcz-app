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
use Illuminate\Support\Collection;

class CompetitionRound extends Model
{
    use HasFactory, HasUuidV7;

    protected $fillable = [
        'competition_detail_id',
        'next_round_id',
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

    public function nextRound(): BelongsTo
    {
        return $this->belongsTo(self::class, 'next_round_id');
    }

    public function previousRound(): ?self
    {
        return self::query()
            ->where('next_round_id', $this->id)
            ->when(
                $this->athlete_category_id !== null,
                fn ($q) => $q->where('athlete_category_id', $this->athlete_category_id)
            )
            ->first();
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
