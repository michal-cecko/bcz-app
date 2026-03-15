<?php

namespace Database\Factories;

use App\Enums\EventTypeEnum;
use App\Models\Event;
use App\Models\EventCategory;
use App\Models\Team;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Event> */
class EventFactory extends Factory
{
    protected $model = Event::class;

    public function definition(): array
    {
        return [
            'event_type' => EventTypeEnum::Report,
            'event_category_id' => EventCategory::factory(),
            'team_id' => Team::factory(),
            'title' => ['sk' => fake()->words(4, true), 'en' => fake()->words(4, true)],
            'card_description' => ['sk' => fake()->sentence(), 'en' => fake()->sentence()],
            'date' => fake()->dateTimeBetween('-1 year', '+1 year'),
            'date_end' => fake()->optional()->dateTimeBetween('+1 day', '+1 year'),
            'country' => 'SK',
            'city' => fake()->city(),
            'attendee_count' => fake()->numberBetween(10, 500),
            'client' => fake()->optional()->company(),
            'is_published' => true,
            'published_at' => now(),
        ];
    }

    public function draft(): static
    {
        return $this->state(fn () => [
            'is_published' => false,
            'published_at' => null,
        ]);
    }

    public function organized(): static
    {
        return $this->state(fn () => [
            'event_type' => EventTypeEnum::Organized,
        ]);
    }

    public function competition(): static
    {
        return $this->state(fn () => [
            'event_type' => EventTypeEnum::Competition,
        ]);
    }
}
