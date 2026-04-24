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
                'title' => [
                    'sk' => 'Platba kartou',
                    'cs' => 'Platba kartou',
                    'en' => 'Card payment',
                ],
                'description' => [
                    'sk' => 'Okamžitá platba cez GoPay. Po zaplatení bude registrácia automaticky potvrdená.',
                    'cs' => 'Okamžitá platba přes GoPay. Po zaplacení bude registrace automaticky potvrzena.',
                    'en' => 'Instant payment via GoPay. Your registration is confirmed right after the payment goes through.',
                ],
                'instructions' => [
                    'sk' => '<p>Po kliknutí na tlačidlo platby ťa presmerujeme do zabezpečenej platobnej brány GoPay. Platbu môžeš dokončiť kartou (Visa, Mastercard) alebo cez tvoju bankovú appku.</p>',
                    'cs' => '<p>Po kliknutí na tlačítko platby tě přesměrujeme do zabezpečené platební brány GoPay. Platbu můžeš dokončit kartou (Visa, Mastercard) nebo přes tvoji bankovní apku.</p>',
                    'en' => '<p>After clicking the payment button we will redirect you to the secure GoPay payment gateway. Pay with card (Visa, Mastercard) or via your banking app.</p>',
                ],
                'sort_order' => 0,
            ],
        );

        $bankTransfer = PaymentMethod::firstOrCreate(
            ['method' => PaymentMethodEnum::BANK_TRANSFER->value],
            [
                'title' => [
                    'sk' => 'Bankový prevod',
                    'cs' => 'Bankovní převod',
                    'en' => 'Bank transfer',
                ],
                'description' => [
                    'sk' => 'Platba na bankový účet. Po výbere zobrazíme QR kód a údaje pre prevod.',
                    'cs' => 'Platba na bankovní účet. Po výběru zobrazíme QR kód a údaje pro převod.',
                    'en' => 'Pay to a bank account. After selecting, we will show a QR code and the transfer details.',
                ],
                'instructions' => [
                    'sk' => '<p>Platbu vykonaj prevodom na zobrazený účet. Použi IBAN, sumu a variabilný symbol tak, ako sú uvedené. Na urýchlenie môžeš naskenovať QR kód vo svojej bankovej aplikácii.</p><p>Registrácia sa potvrdí po pripísaní platby (obvykle 1–2 pracovné dni).</p>',
                    'cs' => '<p>Platbu proveď převodem na zobrazený účet. Použij IBAN, částku a variabilní symbol tak, jak jsou uvedené. Pro urychlení můžeš naskenovat QR kód ve své bankovní aplikaci.</p><p>Registrace se potvrdí po připsání platby (obvykle 1–2 pracovní dny).</p>',
                    'en' => '<p>Make the payment to the displayed account. Use the exact IBAN, amount and variable symbol shown. To speed things up, scan the QR code in your banking app.</p><p>Registration is confirmed once the payment arrives (usually 1–2 business days).</p>',
                ],
                'sort_order' => 1,
            ],
        );

        $cash = PaymentMethod::firstOrCreate(
            ['method' => PaymentMethodEnum::CASH->value],
            [
                'title' => [
                    'sk' => 'Hotovosť',
                    'cs' => 'Hotovost',
                    'en' => 'Cash',
                ],
                'description' => [
                    'sk' => 'Platba v hotovosti na mieste. Kontaktujte organizátora pre dohodnutie termínu.',
                    'cs' => 'Platba v hotovosti na místě. Kontaktujte organizátora pro domluvení termínu.',
                    'en' => 'Pay in cash on site. Contact the organizer to arrange a time.',
                ],
                'instructions' => [
                    'sk' => '<p>Priprav si presnú sumu v hotovosti. Platbu odovzdaj organizátorovi pred začiatkom podujatia alebo tréningu.</p><p>Registrácia bude potvrdená po prijatí platby.</p>',
                    'cs' => '<p>Připrav si přesnou částku v hotovosti. Platbu předej organizátorovi před začátkem akce nebo tréninku.</p><p>Registrace bude potvrzena po přijetí platby.</p>',
                    'en' => '<p>Bring the exact amount in cash. Hand it to the organizer before the event or training starts.</p><p>Registration will be confirmed once the payment is received.</p>',
                ],
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
