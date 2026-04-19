<?php

namespace Database\Seeders;

use App\Enums\PaymentMethodEnum;
use App\Models\PaymentMethod;
use App\Models\Setting;
use App\Models\Team;
use Illuminate\Database\Seeder;

class PaymentMethodSeeder extends Seeder
{
    public function run(): void
    {
        $gopay = PaymentMethod::firstOrCreate(
            ['method' => PaymentMethodEnum::GOPAY->value],
            [
                'title' => 'Platba kartou',
                'description' => 'Okamžitá platba cez GoPay. Po zaplatení bude registrácia automaticky potvrdená.',
                'sort_order' => 0,
            ],
        );

        $bankTransfer = PaymentMethod::firstOrCreate(
            ['method' => PaymentMethodEnum::BANK_TRANSFER->value],
            [
                'title' => 'Bankový prevod',
                'description' => 'Platba na bankový účet. Po výbere zobrazíme QR kód a údaje pre prevod.',
                'sort_order' => 1,
            ],
        );

        $cash = PaymentMethod::firstOrCreate(
            ['method' => PaymentMethodEnum::CASH->value],
            [
                'title' => 'Hotovosť',
                'description' => 'Platba v hotovosti na mieste. Kontaktujte organizátora pre dohodnutie termínu.',
                'sort_order' => 2,
            ],
        );

        $defaultTeamId = Setting::get('default_team_id');

        if ($defaultTeamId) {
            $defaultTeam = Team::find($defaultTeamId);

            if ($defaultTeam) {
                $defaultTeam->paymentMethods()->syncWithoutDetaching([
                    $gopay->id => ['is_enabled' => true, 'sort_order' => 0],
                    $bankTransfer->id => ['is_enabled' => true, 'sort_order' => 1],
                    $cash->id => ['is_enabled' => true, 'sort_order' => 2],
                ]);
            }
        }
    }
}
