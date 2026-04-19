<?php

namespace Database\Factories;

use App\Enums\PairingStrategyEnum;
use App\Enums\RoundAdvancementTypeEnum;
use App\Models\CompetitionDetail;
use App\Models\CompetitionRound;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<CompetitionRound> */
class CompetitionRoundFactory extends Factory
{
    protected $model = CompetitionRound::class;

    public function definition(): array
    {
        return [
            'competition_detail_id' => CompetitionDetail::factory(),
            'round_number' => fake()->numberBetween(1, 5),
            'name' => fake()->randomElement(['Qualification', 'Semi-final', 'Final']),
            'advancement_type' => fake()->randomElement(RoundAdvancementTypeEnum::cases()),
            'team_size' => 1,
            'pairing_strategy' => PairingStrategyEnum::RANDOM,
            'sort_order' => fake()->numberBetween(0, 10),
        ];
    }

    public function qualification(?int $competitorCount = null): self
    {
        return $this->state([
            'advancement_type' => RoundAdvancementTypeEnum::TOP_BY_POINTS,
            'competitor_count' => $competitorCount,
        ]);
    }

    public function battle(int $competitorCount = 4, int $teamSize = 1, PairingStrategyEnum $strategy = PairingStrategyEnum::RANDOM): self
    {
        return $this->state([
            'advancement_type' => RoundAdvancementTypeEnum::BATTLE_WINNER,
            'competitor_count' => $competitorCount,
            'team_size' => $teamSize,
            'pairing_strategy' => $strategy,
        ]);
    }
}
