<?php

namespace Database\Factories;

use App\Models\CompetitionResult;
use App\Models\RoundPart;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<CompetitionResult> */
class CompetitionResultFactory extends Factory
{
    protected $model = CompetitionResult::class;

    public function definition(): array
    {
        return [
            'round_part_id' => RoundPart::factory(),
            'user_id' => User::factory(),
            'score' => fake()->randomFloat(2, 0, 100),
        ];
    }
}
