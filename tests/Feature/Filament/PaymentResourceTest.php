<?php

namespace Tests\Feature\Filament;

use App\Enums\PaymentStatusEnum;
use App\Enums\RegistrationStatusEnum;
use App\Enums\RoleEnum;
use App\Enums\TrainingPricingTypeEnum;
use App\Filament\Resources\Payments\Pages\ListPayments;
use App\Models\Payment;
use App\Models\Team;
use App\Models\Training;
use App\Models\TrainingRegistration;
use App\Models\User;
use App\Notifications\PaymentConfirmed;
use Filament\Actions\Testing\TestAction;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class PaymentResourceTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected User $customer;

    protected Team $team;

    protected function setUp(): void
    {
        parent::setUp();

        foreach (RoleEnum::cases() as $role) {
            Role::firstOrCreate(['name' => $role->value, 'guard_name' => 'web']);
        }

        $this->team = Team::factory()->create();
        $this->admin = User::factory()->create();
        $this->admin->assignRole(RoleEnum::SUPER_ADMIN);
        $this->admin->teams()->attach($this->team);
        $this->customer = User::factory()->create();

        $this->actingAs($this->admin);
        Filament::setCurrentPanel(Filament::getPanel('admin'));
        Filament::setTenant($this->team);
        Filament::bootCurrentPanel();
    }

    private function pendingTrainingPayment(): Payment
    {
        $training = Training::factory()->create([
            'team_id' => $this->team->id,
            'pricing_type' => TrainingPricingTypeEnum::PAID,
            'price_amount' => 25.00,
        ]);

        $registration = TrainingRegistration::factory()->pending()->create([
            'training_id' => $training->id,
            'user_id' => $this->customer->id,
            'payment_due_at' => now()->addDays(7),
        ]);

        return Payment::factory()
            ->pending()
            ->forTrainingRegistration($registration)
            ->create([
                'amount' => 25.00,
                'currency' => 'EUR',
            ]);
    }

    public function test_marking_payment_paid_queues_thank_you_email_by_default(): void
    {
        Notification::fake();

        $payment = $this->pendingTrainingPayment();

        // Note: notify_customer is intentionally omitted to exercise the default-checked toggle.
        Livewire::test(ListPayments::class)
            ->callAction(TestAction::make('edit')->table($payment), data: [
                'status' => PaymentStatusEnum::COMPLETED->value,
            ])
            ->assertHasNoActionErrors();

        $payment->refresh();
        $this->assertSame(PaymentStatusEnum::COMPLETED, $payment->status);
        $this->assertSame(RegistrationStatusEnum::Approved, $payment->payable->status);
        Notification::assertSentTo($this->customer, PaymentConfirmed::class);
    }

    public function test_marking_payment_paid_with_notify_unchecked_does_not_email(): void
    {
        Notification::fake();

        $payment = $this->pendingTrainingPayment();

        Livewire::test(ListPayments::class)
            ->callAction(TestAction::make('edit')->table($payment), data: [
                'status' => PaymentStatusEnum::COMPLETED->value,
                'notify_customer' => false,
            ])
            ->assertHasNoActionErrors();

        $payment->refresh();
        $this->assertSame(PaymentStatusEnum::COMPLETED, $payment->status);
        $this->assertSame(RegistrationStatusEnum::Approved, $payment->payable->status);
        Notification::assertNothingSent();
    }

    public function test_editing_already_paid_payment_does_not_resend_thank_you_email(): void
    {
        Notification::fake();

        $training = Training::factory()->create([
            'team_id' => $this->team->id,
            'pricing_type' => TrainingPricingTypeEnum::PAID,
            'price_amount' => 25.00,
        ]);

        $registration = TrainingRegistration::factory()->create([
            'training_id' => $training->id,
            'user_id' => $this->customer->id,
            'status' => RegistrationStatusEnum::Approved,
        ]);

        $payment = Payment::factory()
            ->forTrainingRegistration($registration)
            ->create([
                'amount' => 25.00,
                'currency' => 'EUR',
            ]);

        // Status stays COMPLETED; only an unrelated field changes, so no email should fire.
        Livewire::test(ListPayments::class)
            ->callAction(TestAction::make('edit')->table($payment), data: [
                'notes' => 'Opravená poznámka',
                'notify_customer' => true,
            ])
            ->assertHasNoActionErrors();

        $this->assertDatabaseHas('payments', [
            'id' => $payment->id,
            'notes' => 'Opravená poznámka',
        ]);
        Notification::assertNothingSent();
    }
}
