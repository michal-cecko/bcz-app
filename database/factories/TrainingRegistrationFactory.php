<?php

namespace Database\Factories;

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
            'form_data' => ['name' => fake()->name(), 'email' => fake()->email()],
            'status' => 'pending',
            'registered_at' => now(),
        ];
    }
}
