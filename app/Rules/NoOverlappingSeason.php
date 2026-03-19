<?php

namespace App\Rules;

use App\Models\TeamSeason;
use Closure;
use Illuminate\Contracts\Validation\DataAwareRule;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Translation\PotentiallyTranslatedString;

class NoOverlappingSeason implements DataAwareRule, ValidationRule
{
    /** @var array<string, mixed> */
    protected array $data = [];

    public function __construct(
        protected string $teamId,
        protected ?string $excludeSeasonId = null,
    ) {}

    /** @param array<string, mixed> $data */
    public function setData(array $data): static
    {
        $this->data = $data;

        return $this;
    }

    /**
     * @param  Closure(string, ?string=): PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $startsAt = $this->data['starts_at'] ?? null;
        $endsAt = $this->data['ends_at'] ?? null;

        if (! $startsAt || ! $endsAt) {
            return;
        }

        $query = TeamSeason::query()
            ->where('team_id', $this->teamId)
            ->where('starts_at', '<', $endsAt)
            ->where('ends_at', '>', $startsAt);

        if ($this->excludeSeasonId) {
            $query->where('id', '!=', $this->excludeSeasonId);
        }

        if ($query->exists()) {
            $fail('Sezóna sa prekrýva s existujúcou sezónou tohto tímu.');
        }
    }
}
