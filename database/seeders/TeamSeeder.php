<?php

namespace Database\Seeders;

use App\Models\Team;
use Illuminate\Database\Seeder;

class TeamSeeder extends Seeder
{
    public function run(): void
    {
        Team::firstOrCreate(
            ['slug' => 'bcz-club'],
            [
                'name' => ['sk' => 'BCZ Club', 'en' => 'BCZ Club', 'cz' => 'BCZ Club'],
                'story' => [
                    'sk' => 'BCZ Club je slovenská organizácia zameraná na parkour a kalistheniku.',
                    'en' => 'BCZ Club is a Slovak organization focused on parkour and calisthenics.',
                ],
                'socials' => [
                    'instagram' => 'https://instagram.com/bczclub',
                    'facebook' => 'https://facebook.com/bczclub',
                    'youtube' => 'https://youtube.com/@bczclub',
                ],
                'is_active' => true,
                'default_locale' => 'sk',
            ],
        );
    }
}
