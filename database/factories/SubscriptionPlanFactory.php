<?php

namespace Database\Factories;

use App\Enums\PlanTierEnum;
use App\Models\SubscriptionPlan;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<SubscriptionPlan> */
class SubscriptionPlanFactory extends Factory
{
    protected $model = SubscriptionPlan::class;

    public function definition(): array
    {
        return [
            'name' => ['sk' => fake()->words(2, true), 'en' => fake()->words(2, true)],
            'tier' => fake()->randomElement(PlanTierEnum::cases()),
            'description' => ['sk' => fake()->sentence(), 'en' => fake()->sentence()],
            'features' => ['sk' => [fake()->sentence(), fake()->sentence()], 'en' => [fake()->sentence(), fake()->sentence()]],
            'limits' => null,
            'is_active' => true,
            'sort_order' => 0,
        ];
    }

    public function free(): static
    {
        return $this->state(fn () => [
            'tier' => PlanTierEnum::FREE,
            'limits' => null,
        ]);
    }

    public function starter(): static
    {
        return $this->state(fn () => [
            'tier' => PlanTierEnum::STARTER,
            'limits' => [
                'max_members' => 50,
                'max_trainings' => 5,
                'max_competitions_yearly' => 2,
                'max_events_yearly' => 5,
            ],
        ]);
    }

    public function pro(): static
    {
        return $this->state(fn () => [
            'tier' => PlanTierEnum::PRO,
            'limits' => [
                'max_members' => 200,
                'max_competitions_yearly' => 10,
                'max_events_yearly' => 20,
            ],
        ]);
    }
}
