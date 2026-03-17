<?php

namespace Database\Factories;

use App\Enums\MembershipStatusEnum;
use App\Models\Membership;
use App\Models\Team;
use App\Models\TeamSeason;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Membership> */
class MembershipFactory extends Factory
{
    protected $model = Membership::class;

    public function definition(): array
    {
        $startsAt = fake()->dateTimeBetween('-6 months', 'now');

        return [
            'team_id' => Team::factory(),
            'user_id' => User::factory(),
            'team_season_id' => null,
            'status' => MembershipStatusEnum::ACTIVE,
            'fee_amount' => fake()->randomFloat(2, 5, 100),
            'fee_currency' => 'EUR',
            'is_free' => false,
            'payment_deadline_at' => null,
            'starts_at' => $startsAt,
            'ends_at' => now()->addYear(),
        ];
    }

    public function expired(): static
    {
        return $this->state(fn () => [
            'status' => MembershipStatusEnum::EXPIRED,
            'starts_at' => now()->subYear(),
            'ends_at' => now()->subMonth(),
        ]);
    }

    public function cancelled(): static
    {
        return $this->state(fn () => [
            'status' => MembershipStatusEnum::CANCELLED,
        ]);
    }

    public function pending(): static
    {
        return $this->state(fn () => [
            'status' => MembershipStatusEnum::PENDING,
        ]);
    }

    public function free(): static
    {
        return $this->state(fn () => [
            'is_free' => true,
            'fee_amount' => 0,
            'status' => MembershipStatusEnum::ACTIVE,
        ]);
    }

    public function forSeason(TeamSeason $season): static
    {
        return $this->state(fn () => [
            'team_id' => $season->team_id,
            'team_season_id' => $season->id,
            'fee_amount' => $season->fee_amount,
            'fee_currency' => $season->fee_currency,
            'starts_at' => $season->starts_at,
            'ends_at' => $season->ends_at,
        ]);
    }
}
