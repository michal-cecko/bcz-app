<?php

namespace Database\Seeders;

use App\Models\SportCategory;
use Illuminate\Database\Seeder;

class SportCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name' => ['sk' => 'Parkour & Freerunning', 'en' => 'Parkour & Freerunning', 'cz' => 'Parkour & Freerunning'],
                'slug' => 'parkour-freerunning',
                'description' => [
                    'sk' => 'Disciplína zameraná na efektívny pohyb cez prekážky.',
                    'en' => 'Discipline focused on efficient movement through obstacles.',
                ],
                'sort_order' => 1,
            ],
            [
                'name' => ['sk' => 'Street Workout', 'en' => 'Street Workout', 'cz' => 'Street Workout'],
                'slug' => 'street-workout',
                'description' => [
                    'sk' => 'Kalisthenické cvičenia s využitím vlastnej váhy tela.',
                    'en' => 'Calisthenics exercises using bodyweight.',
                ],
                'sort_order' => 2,
            ],
        ];

        foreach ($categories as $category) {
            SportCategory::firstOrCreate(
                ['slug' => $category['slug']],
                array_merge($category, ['is_active' => true]),
            );
        }
    }
}
