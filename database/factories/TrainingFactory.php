<?php

namespace Database\Factories;

use App\Enums\TrainingPricingTypeEnum;
use App\Models\City;
use App\Models\SportCategory;
use App\Models\Team;
use App\Models\Training;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Training>
 */
class TrainingFactory extends Factory
{
    protected $model = Training::class;

    public function definition(): array
    {
        return [
            'sport_category_id' => SportCategory::factory(),
            'team_id' => Team::factory(),
            'city_id' => City::factory(),
            'title' => ['sk' => fake()->words(3, true), 'en' => fake()->words(3, true)],
            'description' => ['sk' => fake()->sentence(), 'en' => fake()->sentence()],
            'min_age' => fake()->randomElement([null, 6, 10, 14, 18]),
            'max_age' => fake()->randomElement([null, 10, 14, 18, 25]),
            'duration_minutes' => fake()->randomElement([60, 90, 120]),
            'start_time' => fake()->time('H:i'),
            'schedule_days' => fake()->randomElements(['monday', 'tuesday', 'wednesday', 'thursday', 'friday'], 2),
            'place_name' => ['sk' => fake()->company(), 'en' => fake()->company()],
            'place_address' => fake()->address(),
            'latitude' => fake()->latitude(48.0, 49.5),
            'longitude' => fake()->longitude(16.5, 22.5),
            'max_capacity' => fake()->numberBetween(10, 30),
            'pricing_type' => fake()->randomElement([TrainingPricingTypeEnum::FREE, TrainingPricingTypeEnum::MEMBERSHIP_REQUIRED]),
            'price_amount' => null,
            'is_active' => true,
            'is_recurring' => true,
            'event_date' => null,
            'registration_opens_at' => null,
            'registration_closes_at' => null,
            'sort_order' => fake()->numberBetween(0, 10),
            'registration_form_schema' => [
                ['label' => ['sk' => 'Meno', 'en' => 'First name', 'cs' => 'Jméno'], 'name' => 'meno', 'type' => 'text_input', 'width' => 'half', 'required' => true, 'has_condition' => false],
                ['label' => ['sk' => 'Priezvisko', 'en' => 'Last name', 'cs' => 'Příjmení'], 'name' => 'priezvisko', 'type' => 'text_input', 'width' => 'half', 'required' => true, 'has_condition' => false],
                ['label' => ['sk' => 'Email', 'en' => 'Email', 'cs' => 'Email'], 'name' => 'email', 'type' => 'email', 'width' => 'full', 'required' => true, 'has_condition' => false],
            ],
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn () => ['is_active' => false]);
    }

    public function free(): static
    {
        return $this->state(fn () => [
            'pricing_type' => TrainingPricingTypeEnum::FREE,
            'price_amount' => null,
        ]);
    }

    public function oneTime(): static
    {
        return $this->state(fn () => [
            'is_recurring' => false,
            'event_date' => fake()->dateTimeBetween('+1 week', '+3 months'),
            'schedule_days' => null,
            'pricing_type' => fake()->randomElement([TrainingPricingTypeEnum::FREE, TrainingPricingTypeEnum::PAID]),
            'price_amount' => fake()->randomFloat(2, 5, 50),
        ]);
    }
}
