<?php

namespace Database\Factories;

use App\Models\Competition;
use App\Models\CompetitionRegistration;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<CompetitionRegistration> */
class CompetitionRegistrationFactory extends Factory
{
    protected $model = CompetitionRegistration::class;

    public function definition(): array
    {
        return [
            'competition_id' => Competition::factory(),
            'user_id' => User::factory(),
            'status' => 'pending',
            'registered_at' => now(),
        ];
    }
}
