<?php

namespace Database\Factories;

use App\Enums\MembershipPeriodEnum;
use App\Enums\SubscriptionStatusEnum;
use App\Models\SubscriptionPlan;
use App\Models\Team;
use App\Models\TeamSubscription;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<TeamSubscription> */
class TeamSubscriptionFactory extends Factory
{
    protected $model = TeamSubscription::class;

    public function definition(): array
    {
        return [
            'team_id' => Team::factory(),
            'subscription_plan_id' => SubscriptionPlan::factory(),
            'status' => SubscriptionStatusEnum::ACTIVE,
            'billing_period' => MembershipPeriodEnum::MONTHLY,
            'amount' => fake()->randomFloat(2, 10, 200),
            'currency' => 'EUR',
            'starts_at' => now(),
            'ends_at' => now()->addMonth(),
        ];
    }

    public function cancelled(): static
    {
        return $this->state(fn () => [
            'status' => SubscriptionStatusEnum::CANCELLED,
            'cancelled_at' => now(),
        ]);
    }

    public function expired(): static
    {
        return $this->state(fn () => [
            'status' => SubscriptionStatusEnum::EXPIRED,
            'starts_at' => now()->subYear(),
            'ends_at' => now()->subMonth(),
        ]);
    }
}
