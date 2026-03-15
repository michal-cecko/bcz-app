<?php

namespace Database\Factories;

use App\Enums\EventPricingTypeEnum;
use App\Models\Event;
use App\Models\EventOrganization;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<EventOrganization> */
class EventOrganizationFactory extends Factory
{
    protected $model = EventOrganization::class;

    public function definition(): array
    {
        return [
            'event_id' => Event::factory(),
            'max_capacity' => fake()->numberBetween(20, 200),
            'pricing_type' => EventPricingTypeEnum::Free,
            'is_public_registration' => true,
            'show_countdown' => false,
        ];
    }

    public function paid(float $amount = 25.00, string $currency = 'EUR'): static
    {
        return $this->state(fn () => [
            'pricing_type' => EventPricingTypeEnum::Paid,
            'price_amount' => $amount,
            'price_currency' => $currency,
        ]);
    }

    public function withRegistrationWindow(): static
    {
        return $this->state(fn () => [
            'registration_opens_at' => now()->subWeek(),
            'registration_closes_at' => now()->addMonth(),
        ]);
    }
}
