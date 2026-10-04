<?php

namespace Tests\Feature\Notifications;

use App\Enums\PaymentMethodEnum;
use App\Enums\RoleEnum;
use App\Models\Membership;
use App\Models\PaymentMethod;
use App\Models\Team;
use App\Notifications\MembershipPaymentDue;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use PHPUnit\Framework\Attributes\DataProvider;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class MembershipPaymentDueCashNoticeTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        foreach (RoleEnum::cases() as $role) {
            Role::firstOrCreate(['name' => $role->value, 'guard_name' => 'web']);
        }

        Notification::fake();
    }

    /** @return array<string, array{string, bool}> */
    public static function cashMethodConfigurations(): array
    {
        return [
            'no team methods' => ['no_methods', false],
            'bank transfer only' => ['unattached', false],
            'cash belongs to another team' => ['other_team', false],
            'cash disabled by team' => ['disabled', false],
            'cash globally inactive' => ['inactive', false],
            'cash enabled for team' => ['enabled', true],
        ];
    }

    #[DataProvider('cashMethodConfigurations')]
    public function test_cash_notice_follows_the_membership_teams_enabled_payment_methods(string $configuration, bool $expectCashNotice): void
    {
        $team = Team::factory()->create();
        $bank = PaymentMethod::create([
            'method' => PaymentMethodEnum::BANK_TRANSFER,
            'title' => ['sk' => 'Bankový prevod'],
            'is_active' => true,
        ]);
        $cash = PaymentMethod::create([
            'method' => PaymentMethodEnum::CASH,
            'title' => ['sk' => 'Hotovosť'],
            'is_active' => $configuration !== 'inactive',
        ]);

        if ($configuration !== 'no_methods') {
            $team->paymentMethods()->attach($bank->id, ['is_enabled' => true]);
        }

        if (in_array($configuration, ['enabled', 'disabled', 'inactive'], true)) {
            $team->paymentMethods()->attach($cash->id, ['is_enabled' => $configuration !== 'disabled']);
        } elseif ($configuration === 'other_team') {
            Team::factory()->create()->paymentMethods()->attach($cash->id, ['is_enabled' => true]);
        }

        $membership = Membership::factory()->pending()->create([
            'team_id' => $team->id,
            'fee_amount' => 80.00,
            'fee_currency' => 'EUR',
        ]);

        $html = (new MembershipPaymentDue($membership))->toMail($membership->user)->render();
        $cashNotice = 'Váš tím umožňuje platbu v hotovosti.';

        if ($expectCashNotice) {
            $this->assertStringContainsString($cashNotice, $html);
        } else {
            $this->assertStringNotContainsString($cashNotice, $html);
        }

        $this->assertStringContainsString('80.00 EUR', $html);
        $this->assertStringContainsString('Zaplatiť', $html);
    }
}
