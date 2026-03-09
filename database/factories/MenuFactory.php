<?php

namespace Database\Factories;

use App\Enums\MenuLocationEnum;
use App\Models\Menu;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Menu>
 */
class MenuFactory extends Factory
{
    protected $model = Menu::class;

    public function definition(): array
    {
        return [
            'location' => fake()->randomElement(MenuLocationEnum::cases()),
            'label' => ['sk' => fake()->words(2, true), 'en' => fake()->words(2, true)],
            'items' => [],
        ];
    }
}
