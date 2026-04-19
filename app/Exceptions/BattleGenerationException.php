<?php

namespace App\Exceptions;

use RuntimeException;

class BattleGenerationException extends RuntimeException
{
    public static function alreadyExists(int $count): self
    {
        return new self(__('battle.errors.already_exists', ['count' => $count]));
    }

    public static function invalidAdvancementType(): self
    {
        return new self(__('battle.errors.invalid_advancement_type'));
    }

    public static function invalidCompetitorCount(int $count, int $teamSize): self
    {
        return new self(__('battle.errors.invalid_competitor_count', [
            'count' => $count,
            'team_size' => $teamSize,
            'slots' => $teamSize * 2,
        ]));
    }

    public static function insufficientCompetitors(int $have, int $need): self
    {
        return new self(__('battle.errors.insufficient_competitors', [
            'have' => $have,
            'need' => $need,
        ]));
    }

    public static function missingCompetitorCount(): self
    {
        return new self(__('battle.errors.missing_competitor_count'));
    }

    public static function thirdPlaceRequiresBattleSource(): self
    {
        return new self(__('battle.errors.third_place_requires_battle_source'));
    }

    public static function thirdPlaceRequiresTwoSources(): self
    {
        return new self(__('battle.errors.third_place_requires_two_sources'));
    }

    public static function thirdPlaceNeedsCompleteWinners(?int $bracketPosition = null): self
    {
        $key = $bracketPosition === null
            ? 'battle.errors.third_place_needs_complete_winners'
            : 'battle.errors.third_place_unresolved_battle';

        return new self(__($key, ['bracket' => $bracketPosition]));
    }
}
