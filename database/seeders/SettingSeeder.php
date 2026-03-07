<?php

namespace Database\Seeders;

use App\Enums\SettingTypeEnum;
use App\Models\Setting;
use App\Models\Team;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        $defaultTeam = Team::query()->where('slug', 'bcz-club')->first();

        $settings = [
            [
                'key' => 'default_team_id',
                'label' => ['sk' => 'Predvolený tím', 'en' => 'Default Team'],
                'description' => ['sk' => 'Tím zobrazený na verejnej stránke.', 'en' => 'Team displayed on the public site.'],
                'type' => SettingTypeEnum::TEAM_SELECT,
                'value' => $defaultTeam?->id,
                'is_exposed' => false,
            ],
            [
                'key' => 'available_locales',
                'label' => ['sk' => 'Dostupné jazyky', 'en' => 'Available Locales'],
                'description' => ['sk' => 'Jazyky dostupné na stránke.', 'en' => 'Languages available on the site.'],
                'type' => SettingTypeEnum::MULTI_SELECT,
                'options' => ['sk', 'en', 'cz'],
                'value' => ['sk', 'en', 'cz'],
                'is_exposed' => true,
            ],
            [
                'key' => 'default_locale',
                'label' => ['sk' => 'Predvolený jazyk', 'en' => 'Default Locale'],
                'description' => ['sk' => 'Jazyk použitý pri prvej návšteve.', 'en' => 'Language used on first visit.'],
                'type' => SettingTypeEnum::SELECT,
                'options' => ['sk', 'en', 'cz'],
                'value' => 'sk',
                'is_exposed' => true,
            ],
        ];

        foreach ($settings as $setting) {
            Setting::query()->updateOrCreate(
                ['key' => $setting['key']],
                $setting,
            );
        }
    }
}
