<?php

namespace App\Models;

use App\Enums\RoundAdvancementTypeEnum;
use App\Enums\TimetableEntryStatusEnum;
use App\Enums\TimetableEntryTypeEnum;
use App\Models\Concerns\HasUuidV7;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Collection;
use Spatie\Translatable\HasTranslations;

class TimetableEntry extends Model
{
    use HasFactory, HasTranslations, HasUuidV7;

    /** @var list<string> */
    public array $translatable = ['title', 'description'];

    protected $fillable = [
        'competition_detail_id',
        'title',
        'description',
        'type',
        'competition_round_id',
        'scheduled_time',
        'actual_start_time',
        'actual_end_time',
        'status',
        'current_competitor_index',
        'current_battle_id',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'scheduled_time' => 'datetime',
            'actual_start_time' => 'datetime',
            'actual_end_time' => 'datetime',
            'status' => TimetableEntryStatusEnum::class,
            'type' => TimetableEntryTypeEnum::class,
            'sort_order' => 'integer',
            'current_competitor_index' => 'integer',
        ];
    }

    public function competitionDetail(): BelongsTo
    {
        return $this->belongsTo(CompetitionDetail::class);
    }

    public function competitionRound(): BelongsTo
    {
        return $this->belongsTo(CompetitionRound::class);
    }

    public function currentBattle(): BelongsTo
    {
        return $this->belongsTo(Battle::class, 'current_battle_id');
    }

    /**
     * Calculate delay in minutes (positive = behind schedule).
     */
    public function getDelayMinutes(): int
    {
        if ($this->status !== TimetableEntryStatusEnum::IN_PROGRESS) {
            return 0;
        }

        if ($this->actual_start_time && $this->scheduled_time) {
            return max(0, (int) $this->scheduled_time->diffInMinutes($this->actual_start_time, false));
        }

        return 0;
    }

    /**
     * Get ordered competitors for qualification rounds.
     *
     * @return Collection<int, EventRegistration>
     */
    public function getOrderedCompetitors(): Collection
    {
        return $this->competitionRound?->getOrderedCompetitors() ?? collect();
    }

    /**
     * Get the currently active competitor for qualification rounds.
     */
    public function getCurrentCompetitor(): ?EventRegistration
    {
        if ($this->current_competitor_index === null) {
            return null;
        }

        return $this->getOrderedCompetitors()->get($this->current_competitor_index);
    }

    /**
     * Whether this entry's round is a battle round.
     */
    public function isBattleRound(): bool
    {
        return $this->competitionRound?->advancement_type === RoundAdvancementTypeEnum::BATTLE_WINNER;
    }

    /**
     * Get a display label for the current performer.
     */
    public function getCurrentPerformerLabel(): ?string
    {
        if ($this->status !== TimetableEntryStatusEnum::IN_PROGRESS
            || $this->type !== TimetableEntryTypeEnum::COMPETITION_ROUND
            || ! $this->competitionRound) {
            return null;
        }

        if ($this->isBattleRound()) {
            $battle = $this->currentBattle;
            if (! $battle) {
                return null;
            }

            return "Battle {$battle->bracket_position}: {$battle->getCompetitorALabel()} vs {$battle->getCompetitorBLabel()}";
        }

        $competitor = $this->getCurrentCompetitor();
        if (! $competitor) {
            return null;
        }

        $total = $this->getOrderedCompetitors()->count();
        $index = $this->current_competitor_index + 1;

        return "{$competitor->user?->name} ({$index}/{$total})";
    }

    /**
     * Reset competitor/battle tracking.
     */
    public function resetTracking(): void
    {
        $this->update([
            'current_competitor_index' => null,
            'current_battle_id' => null,
        ]);
    }

    /**
     * Initialize tracking to the first competitor or battle.
     */
    public function initializeTracking(): void
    {
        if ($this->type !== TimetableEntryTypeEnum::COMPETITION_ROUND || ! $this->competitionRound) {
            return;
        }

        if ($this->isBattleRound()) {
            $this->update(['current_battle_id' => $this->competitionRound->battles->first()?->id]);
        } else {
            $this->update(['current_competitor_index' => 0]);
        }
    }
}
