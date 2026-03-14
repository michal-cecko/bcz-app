<?php

namespace Database\Factories;

use App\Enums\JoinRequestStatusEnum;
use App\Models\Team;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TeamJoinRequest>
 */
class TeamJoinRequestFactory extends Factory
{
    public function definition(): array
    {
        return [
            'team_id' => Team::factory(),
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'status' => JoinRequestStatusEnum::Pending,
        ];
    }

    public function approved(): static
    {
        return $this->state([
            'status' => JoinRequestStatusEnum::Approved,
            'processed_at' => now(),
        ]);
    }

    public function rejected(): static
    {
        return $this->state([
            'status' => JoinRequestStatusEnum::Rejected,
            'processed_at' => now(),
        ]);
    }
}
