<?php

namespace Database\Factories;

use App\Enums\ProfileTypeEnum;
use App\Models\ProfileGalleryItem;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ProfileGalleryItem>
 */
class ProfileGalleryItemFactory extends Factory
{
    protected $model = ProfileGalleryItem::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'profile_type' => fake()->randomElement(ProfileTypeEnum::cases()),
            'description' => ['sk' => fake()->sentence(), 'en' => fake()->sentence()],
            'tags' => [fake()->word(), fake()->word()],
            'sort_order' => 0,
            'is_approved' => false,
        ];
    }

    public function approved(): static
    {
        return $this->state(fn () => [
            'is_approved' => true,
        ]);
    }
}
