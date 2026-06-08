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
            [
                'key' => 'gopay_platform_fee_percent',
                'label' => ['sk' => 'Poplatok platformy (GoPay)', 'en' => 'Platform Fee (GoPay)'],
                'description' => ['sk' => 'Percentuálny poplatok z platieb cez GoPay.', 'en' => 'Percentage fee from GoPay payments.'],
                'type' => SettingTypeEnum::NUMBER,
                'value' => 5,
                'is_exposed' => false,
            ],
            [
                'key' => 'supported_currencies',
                'label' => ['sk' => 'Podporované meny', 'en' => 'Supported Currencies'],
                'description' => ['sk' => 'Dostupné meny pre platby.', 'en' => 'Available currencies for payments.'],
                'type' => SettingTypeEnum::MULTI_SELECT,
                'options' => ['EUR', 'CZK', 'USD', 'GBP', 'PLN'],
                'value' => ['EUR', 'CZK', 'USD'],
                'is_exposed' => false,
            ],
            [
                'key' => 'topbar_show_until',
                'label' => ['sk' => 'Zobrazovať top bar do', 'en' => 'Show top bar until'],
                'description' => ['sk' => 'Dátum, do ktorého sa zobrazí rebranding lišta v hlavičke. Prázdne = skrytá.', 'en' => 'Date until which the rebranding bar is shown in the header. Empty = hidden.'],
                'type' => SettingTypeEnum::DATE,
                'value' => null,
                'is_exposed' => false,
            ],
            [
                'key' => 'rebranding_modal_show_until',
                'label' => ['sk' => 'Zobrazovať rebranding modal do', 'en' => 'Show rebranding modal until'],
                'description' => ['sk' => 'Dátum, do ktorého sa zobrazí rebranding modal na frontende. Prázdne = skrytý.', 'en' => 'Date until which the rebranding modal is shown on frontend. Empty = hidden.'],
                'type' => SettingTypeEnum::DATE,
                'value' => null,
                'is_exposed' => false,
            ],
            [
                'key' => 'default_og_image',
                'label' => ['sk' => 'Predvolený obrázok pre zdieľanie (OG)', 'en' => 'Default Sharing Image (OG)'],
                'description' => ['sk' => 'Náhľadový obrázok, ktorý sa zobrazí pri zdieľaní odkazu na stránku na sociálnych sieťach (Facebook, Instagram, X…). Odporúčaný rozmer 1200×630 px.', 'en' => 'Preview image shown when a page link is shared on social media (Facebook, Instagram, X…). Recommended size 1200×630 px.'],
                'type' => SettingTypeEnum::IMAGE,
                'value' => null,
                'is_exposed' => true,
            ],
            [
                'key' => 'social_instagram_url',
                'label' => ['sk' => 'Instagram URL', 'en' => 'Instagram URL'],
                'description' => ['sk' => 'Predvolená URL adresa Instagram profilu.', 'en' => 'Default Instagram profile URL.'],
                'type' => SettingTypeEnum::TEXT,
                'value' => 'https://www.instagram.com/bfreak.sk',
                'is_exposed' => true,
            ],
            [
                'key' => 'social_facebook_url',
                'label' => ['sk' => 'Facebook URL', 'en' => 'Facebook URL'],
                'description' => ['sk' => 'Predvolená URL adresa Facebook stránky.', 'en' => 'Default Facebook page URL.'],
                'type' => SettingTypeEnum::TEXT,
                'value' => 'https://www.facebook.com/bfreak.sk',
                'is_exposed' => true,
            ],
            [
                'key' => 'social_youtube_url',
                'label' => ['sk' => 'YouTube URL', 'en' => 'YouTube URL'],
                'description' => ['sk' => 'Predvolená URL adresa YouTube kanálu.', 'en' => 'Default YouTube channel URL.'],
                'type' => SettingTypeEnum::TEXT,
                'value' => 'https://www.youtube.com/@bfreak',
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
