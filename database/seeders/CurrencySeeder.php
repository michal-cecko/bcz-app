<?php

namespace Database\Seeders;

use App\Models\Currency;
use Illuminate\Database\Seeder;

class CurrencySeeder extends Seeder
{
    public function run(): void
    {
        $currencies = [
            [
                'code' => 'EUR',
                'name' => ['sk' => 'Euro', 'en' => 'Euro', 'cs' => 'Euro'],
                'symbol' => '€',
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'code' => 'CZK',
                'name' => ['sk' => 'Česká koruna', 'en' => 'Czech Koruna', 'cs' => 'Česká koruna'],
                'symbol' => 'Kč',
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'code' => 'USD',
                'name' => ['sk' => 'Americký dolár', 'en' => 'US Dollar', 'cs' => 'Americký dolar'],
                'symbol' => '$',
                'is_active' => true,
                'sort_order' => 3,
            ],
        ];

        foreach ($currencies as $currency) {
            Currency::query()->updateOrCreate(
                ['code' => $currency['code']],
                $currency,
            );
        }
    }
}
