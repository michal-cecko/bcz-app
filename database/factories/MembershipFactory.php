<?php

namespace Database\Factories;

use App\Enums\MembershipPeriodEnum;
use App\Enums\MembershipStatusEnum;
use App\Models\Membership;
use App\Models\Team;
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
            'status' => MembershipStatusEnum::ACTIVE,
            'period' => fake()->randomElement(MembershipPeriodEnum::cases()),
            'fee_amount' => fake()->randomFloat(2, 5, 100),
            'fee_currency' => 'EUR',
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
}
