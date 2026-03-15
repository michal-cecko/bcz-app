<?php

namespace Database\Factories;

use App\Enums\RegistrationStatusEnum;
use App\Models\Training;
use App\Models\TrainingRegistration;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<TrainingRegistration>
 */
class TrainingRegistrationFactory extends Factory
{
    protected $model = TrainingRegistration::class;

    public function definition(): array
    {
        return [
            'training_id' => Training::factory(),
            'user_id' => User::factory(),
            'form_data' => [],
            'status' => RegistrationStatusEnum::Approved,
            'registered_at' => now(),
        ];
    }

    public function approved(): static
    {
        return $this->state(fn () => ['status' => RegistrationStatusEnum::Approved]);
    }

    public function pending(): static
    {
        return $this->state(fn () => ['status' => RegistrationStatusEnum::Pending]);
    }

    public function cancelled(): static
    {
        return $this->state(fn () => ['status' => RegistrationStatusEnum::Cancelled]);
    }

    public function forTraining(Training $training): static
    {
        return $this->state(function () use ($training): array {
            $formData = [];

            foreach ($training->registration_form_schema ?? [] as $field) {
                $formData[$field['name']] = match ($field['type'] ?? 'text_input') {
                    'email' => fake()->email(),
                    'phone' => '+421 '.fake()->numerify('### ### ###'),
                    'number_input' => (string) fake()->numberBetween(8, 35),
                    'year_picker' => (string) fake()->numberBetween(1990, 2015),
                    'textarea' => fake()->optional(0.3)->sentence(),
                    'select' => $this->randomOption($field['options'] ?? ''),
                    'multi_select' => $this->randomOption($field['options'] ?? ''),
                    'date_picker' => fake()->date(),
                    'time_picker' => fake()->time('H:i'),
                    default => fake()->firstName(),
                };
            }

            return ['form_data' => $formData, 'training_id' => $training->id];
        });
    }

    private function randomOption(string|array $options): string
    {
        if (is_array($options)) {
            $opts = $options;
        } else {
            $opts = array_map('trim', explode(',', $options));
        }

        return ! empty($opts) ? fake()->randomElement($opts) : '';
    }
}
