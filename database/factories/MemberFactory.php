<?php

namespace Database\Factories;

use App\Models\Member;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Member> */
class MemberFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(), 'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(), 'birth_date' => fake()->dateTimeBetween('-17 years', '-3 years'),
            'has_free_membership' => false,
        ];
    }
}
