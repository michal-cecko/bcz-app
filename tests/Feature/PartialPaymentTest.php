<?php

namespace Tests\Feature;

use App\Enums\MembershipStatusEnum;
use App\Enums\PaymentMethodEnum;
use App\Enums\PaymentStatusEnum;
use App\Enums\RegistrationStatusEnum;
use App\Enums\RoleEnum;
use App\Enums\TrainingPricingTypeEnum;
use App\Filament\RelationManagers\RegistrationPaymentsRelationManager;
use App\Filament\Resources\EventRegistrations\Pages\ViewEventRegistration;
use App\Filament\Resources\Events\Pages\EditEvent;
use App\Filament\Resources\Events\RelationManagers\RegistrationsRelationManager as EventRegistrationsRelationManager;
use App\Filament\Resources\TrainingRegistrations\Pages\ViewTrainingRegistration;
use App\Filament\Resources\Trainings\Pages\EditTraining;
use App\Filament\Resources\Trainings\RelationManagers\RegistrationsRelationManager as TrainingRegistrationsRelationManager;
use App\Models\Event;
use App\Models\EventOrganization;
use App\Models\EventRegistration;
use App\Models\Membership;
use App\Models\Payment;
use App\Models\RegistrationFee;
use App\Models\Team;
use App\Models\TeamSeason;
use App\Models\Training;
use App\Models\TrainingRegistration;
use App\Models\User;
use App\Services\PaymentService;
use Filament\Actions\Testing\TestAction;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class PartialPaymentTest extends TestCase
{
    use RefreshDatabase;

    private Team $team;

    private PaymentService $payments;

    protected function setUp(): void
    {
        parent::setUp();

        Notification::fake();

        foreach (RoleEnum::cases() as $role) {
            Role::firstOrCreate(['name' => $role->value, 'guard_name' => 'web']);
        }

        $this->team = Team::factory()->create();
        $this->payments = app(PaymentService::class);

        $admin = User::factory()->create();
        $admin->assignRole(RoleEnum::SUPER_ADMIN);
        $admin->teams()->attach($this->team);

        $this->actingAs($admin);
        Filament::setCurrentPanel(Filament::getPanel('admin'));
        Filament::setTenant($this->team);
        Filament::bootCurrentPanel();
    }

    private function pendingTrainingRegistration(float $price = 100.00): TrainingRegistration
    {
        $training = Training::factory()->create([
            'team_id' => $this->team->id,
            'pricing_type' => TrainingPricingTypeEnum::PAID,
            'price_amount' => $price,
        ]);

        return TrainingRegistration::factory()->pending()->create(['training_id' => $training->id]);
    }

    private function pendingEventRegistrationWithCategoryFee(float $orgPrice, float $categoryFee): EventRegistration
    {
        $event = Event::factory()->competition()->create(['team_id' => $this->team->id]);
        EventOrganization::factory()->paid($orgPrice)->create(['event_id' => $event->id]);
        $fee = RegistrationFee::factory()->create(['amount' => $categoryFee, 'currency' => 'EUR']);

        return EventRegistration::factory()->create([
            'event_id' => $event->id,
            'registration_fee_id' => $fee->id,
            'status' => RegistrationStatusEnum::Pending,
        ]);
    }

    private function recordOnTrainingRow(TrainingRegistration $registration, float $amount): void
    {
        Livewire::test(TrainingRegistrationsRelationManager::class, [
            'ownerRecord' => $registration->training,
            'pageClass' => EditTraining::class,
        ])
            ->callAction(TestAction::make('record_payment')->table($registration), [
                'amount' => $amount,
                'payment_method' => PaymentMethodEnum::CASH->value,
                'payment_status' => PaymentStatusEnum::COMPLETED->value,
            ])
            ->assertHasNoActionErrors();
    }

    public function test_training_row_action_approves_only_once_the_price_is_covered(): void
    {
        $registration = $this->pendingTrainingRegistration(100.00);

        $this->recordOnTrainingRow($registration, 30.00);
        $this->assertSame(RegistrationStatusEnum::Pending, $registration->refresh()->status);

        $this->recordOnTrainingRow($registration, 70.00);
        $this->assertSame(RegistrationStatusEnum::Approved, $registration->refresh()->status);
    }

    public function test_training_row_action_suggests_what_is_still_owed(): void
    {
        $registration = $this->pendingTrainingRegistration(100.00);
        $this->recordOnTrainingRow($registration, 30.00);

        Livewire::test(TrainingRegistrationsRelationManager::class, [
            'ownerRecord' => $registration->training,
            'pageClass' => EditTraining::class,
        ])
            ->mountAction(TestAction::make('record_payment')->table($registration))
            ->assertActionDataSet(['amount' => 70.0]);
    }

    public function test_training_registration_page_action_does_not_approve_a_partial_payment(): void
    {
        $registration = $this->pendingTrainingRegistration(100.00);

        Livewire::test(ViewTrainingRegistration::class, ['record' => $registration->getRouteKey()])
            ->callAction('record_payment', [
                'amount' => 30.00,
                'payment_method' => PaymentMethodEnum::CASH->value,
                'payment_status' => PaymentStatusEnum::COMPLETED->value,
            ])
            ->assertHasNoActionErrors();

        $this->assertSame(RegistrationStatusEnum::Pending, $registration->refresh()->status);
    }

    public function test_event_row_action_checks_against_the_category_fee_not_the_event_price(): void
    {
        $registration = $this->pendingEventRegistrationWithCategoryFee(orgPrice: 40.00, categoryFee: 60.00);

        Livewire::test(EventRegistrationsRelationManager::class, [
            'ownerRecord' => $registration->event,
            'pageClass' => EditEvent::class,
        ])
            ->callAction(TestAction::make('record_payment')->table($registration), [
                'amount' => 40.00,
                'payment_method' => PaymentMethodEnum::CASH->value,
                'payment_status' => PaymentStatusEnum::COMPLETED->value,
            ])
            ->assertHasNoActionErrors();

        $this->assertSame(RegistrationStatusEnum::Pending, $registration->refresh()->status);
    }

    public function test_event_registration_page_action_does_not_approve_a_partial_payment(): void
    {
        $registration = $this->pendingEventRegistrationWithCategoryFee(orgPrice: 60.00, categoryFee: 60.00);

        Livewire::test(ViewEventRegistration::class, ['record' => $registration->getRouteKey()])
            ->callAction('record_payment', [
                'amount' => 20.00,
                'payment_method' => PaymentMethodEnum::CASH->value,
                'payment_status' => PaymentStatusEnum::COMPLETED->value,
            ])
            ->assertHasNoActionErrors();

        $this->assertSame(RegistrationStatusEnum::Pending, $registration->refresh()->status);
    }

    public function test_registration_payments_tab_ignores_other_currencies_and_uses_the_category_fee(): void
    {
        $registration = $this->pendingEventRegistrationWithCategoryFee(orgPrice: 40.00, categoryFee: 60.00);

        $tab = Livewire::test(RegistrationPaymentsRelationManager::class, [
            'ownerRecord' => $registration,
            'pageClass' => ViewEventRegistration::class,
        ]);

        $tab->callAction(TestAction::make('create')->table(), [
            'amount' => 100.00,
            'currency' => 'CZK',
            'payment_method' => PaymentMethodEnum::CASH->value,
            'status' => PaymentStatusEnum::COMPLETED->value,
        ])->assertHasNoActionErrors();

        $tab->callAction(TestAction::make('create')->table(), [
            'amount' => 40.00,
            'currency' => 'EUR',
            'payment_method' => PaymentMethodEnum::CASH->value,
            'status' => PaymentStatusEnum::COMPLETED->value,
        ])->assertHasNoActionErrors();

        $this->assertSame(RegistrationStatusEnum::Pending, $registration->refresh()->status);

        $tab->callAction(TestAction::make('create')->table(), [
            'amount' => 20.00,
            'currency' => 'EUR',
            'payment_method' => PaymentMethodEnum::CASH->value,
            'status' => PaymentStatusEnum::COMPLETED->value,
        ])->assertHasNoActionErrors();

        $this->assertSame(RegistrationStatusEnum::Approved, $registration->refresh()->status);
    }

    public function test_a_partial_payment_lowers_the_open_payment_and_full_payment_cancels_it(): void
    {
        $registration = $this->pendingTrainingRegistration(100.00);
        $open = $this->payments->createPendingPayment($registration->user, $this->team, $registration, 100.00);

        $this->recordOnTrainingRow($registration, 30.00);
        $this->assertEquals(70.00, (float) $open->refresh()->amount);
        $this->assertSame(PaymentStatusEnum::PENDING, $open->status);

        $this->recordOnTrainingRow($registration, 70.00);
        $this->assertSame(PaymentStatusEnum::CANCELLED, $open->refresh()->status);
    }

    public function test_membership_payment_box_asks_only_for_the_rest_after_a_partial_payment(): void
    {
        $member = User::factory()->create();
        $membership = Membership::factory()->pending()->create([
            'team_id' => $this->team->id,
            'user_id' => $member->id,
            'fee_amount' => 200.00,
            'fee_currency' => 'EUR',
        ]);

        $this->payments->recordManualPayment($member, $this->team, $membership, 80.00, 'EUR', PaymentMethodEnum::CASH, notify: false);

        $open = $this->payments->ensurePendingPaymentFor($member, $this->team, $membership, 200.00);

        $this->assertEquals(120.00, (float) $open->amount);
        $this->assertSame(MembershipStatusEnum::PENDING, $membership->refresh()->status);
    }

    public function test_refunding_a_payment_that_completed_the_price_reopens_the_registration(): void
    {
        $registration = $this->pendingTrainingRegistration(100.00);
        $payment = $this->payments->recordManualPayment($registration->user, $this->team, $registration, 100.00, 'EUR', PaymentMethodEnum::CASH, notify: false);
        $this->assertSame(RegistrationStatusEnum::Approved, $registration->refresh()->status);

        $this->payments->refund($payment);

        $this->assertSame(RegistrationStatusEnum::Pending, $registration->refresh()->status);
    }

    public function test_lowering_or_deleting_a_counted_payment_reopens_the_membership(): void
    {
        $member = User::factory()->create();
        $membership = Membership::factory()->pending()->create([
            'team_id' => $this->team->id,
            'user_id' => $member->id,
            'fee_amount' => 100.00,
            'fee_currency' => 'EUR',
        ]);
        $payment = $this->payments->recordManualPayment($member, $this->team, $membership, 100.00, 'EUR', PaymentMethodEnum::CASH, notify: false);
        $this->assertSame(MembershipStatusEnum::ACTIVE, $membership->refresh()->status);

        $payment->update(['amount' => 40.00]);
        $this->assertSame(MembershipStatusEnum::PENDING, $membership->refresh()->status);

        $payment->update(['amount' => 100.00]);
        $this->payments->processPaymentCompleted($payment->refresh(), notify: false);
        $this->assertSame(MembershipStatusEnum::ACTIVE, $membership->refresh()->status);

        $payment->delete();
        $this->assertSame(MembershipStatusEnum::PENDING, $membership->refresh()->status);
    }

    public function test_deleting_a_payment_that_never_counted_leaves_a_manual_approval_alone(): void
    {
        $registration = $this->pendingTrainingRegistration(100.00);
        $registration->update(['status' => RegistrationStatusEnum::Approved]);
        $open = $this->payments->createPendingPayment($registration->user, $this->team, $registration, 100.00);

        $open->delete();

        $this->assertSame(RegistrationStatusEnum::Approved, $registration->refresh()->status);
    }

    public function test_free_registrations_are_never_reopened(): void
    {
        $registration = $this->pendingEventRegistrationWithCategoryFee(orgPrice: 0.00, categoryFee: 0.00);
        $registration->update(['status' => RegistrationStatusEnum::Approved]);
        $payment = Payment::factory()->create([
            'payable_type' => $registration->getMorphClass(),
            'payable_id' => $registration->id,
            'amount' => 10.00,
        ]);

        $payment->delete();

        $this->assertSame(RegistrationStatusEnum::Approved, $registration->refresh()->status);
    }

    public function test_paying_in_full_leaves_an_open_gopay_payment_alone(): void
    {
        $registration = $this->pendingTrainingRegistration(100.00);
        $gopay = Payment::factory()->gopay()->create([
            'payable_type' => $registration->getMorphClass(),
            'payable_id' => $registration->id,
            'amount' => 100.00,
            'status' => PaymentStatusEnum::PENDING,
        ]);

        $this->recordOnTrainingRow($registration, 100.00);

        $this->assertSame(PaymentStatusEnum::PENDING, $gopay->refresh()->status);
    }

    public function test_several_open_payments_ask_for_the_rest_only_once(): void
    {
        $registration = $this->pendingTrainingRegistration(150.00);
        $older = $this->payments->createPendingPayment($registration->user, $this->team, $registration, 50.00);
        $this->travel(1)->minutes();
        $newer = $this->payments->createPendingPayment($registration->user, $this->team, $registration, 50.00);

        $this->recordOnTrainingRow($registration, 50.00);

        $this->assertSame(PaymentStatusEnum::CANCELLED, $older->refresh()->status);
        $this->assertEquals(100.00, (float) $newer->refresh()->amount);
    }

    public function test_a_reopened_registration_gets_a_new_due_date(): void
    {
        $registration = $this->pendingTrainingRegistration(100.00);
        $payment = $this->payments->recordManualPayment($registration->user, $this->team, $registration, 100.00, 'EUR', PaymentMethodEnum::CASH, notify: false);
        $this->assertNull($registration->refresh()->payment_due_at);

        $this->payments->refund($payment);

        $registration->refresh();
        $this->assertSame(RegistrationStatusEnum::Pending, $registration->status);
        $this->assertTrue($registration->payment_due_at->isSameDay(now()->addDays(7)));
    }

    public function test_a_reopened_registration_is_reminded_again(): void
    {
        $registration = $this->pendingTrainingRegistration(100.00);
        $payment = $this->payments->recordManualPayment($registration->user, $this->team, $registration, 100.00, 'EUR', PaymentMethodEnum::CASH, notify: false);
        $registration->update(['payment_reminder_sent_at' => now()->subDays(3)]);

        $this->payments->refund($payment);

        $this->assertNull($registration->refresh()->payment_reminder_sent_at);
    }

    public function test_a_reopened_membership_gets_a_new_deadline_and_reminder(): void
    {
        $member = User::factory()->create();
        $membership = Membership::factory()->pending()->create([
            'team_id' => $this->team->id,
            'user_id' => $member->id,
            'fee_amount' => 100.00,
            'fee_currency' => 'EUR',
            'payment_deadline_at' => now()->subMonth(),
            'payment_reminder_sent_at' => now()->subMonth(),
        ]);
        $payment = $this->payments->recordManualPayment($member, $this->team, $membership, 100.00, 'EUR', PaymentMethodEnum::CASH, notify: false);

        $payment->delete();

        $membership->refresh();
        $this->assertSame(MembershipStatusEnum::PENDING, $membership->status);
        $this->assertTrue($membership->payment_deadline_at->isFuture());
        $this->assertNull($membership->payment_reminder_sent_at);
    }

    public function test_no_open_payment_is_issued_when_nothing_is_owed(): void
    {
        $member = User::factory()->create();
        $member->assignRole(RoleEnum::CUSTOMER->value);
        $season = TeamSeason::factory()->create(['team_id' => $this->team->id, 'fee_amount' => 0.00]);

        $this->assertNull($this->payments->ensurePendingMembershipPayment($member, $this->team, $season));
        $this->assertSame(0, Payment::count());
    }

    public function test_the_open_payment_recorded_for_a_guest_is_reused_not_duplicated(): void
    {
        $registration = $this->pendingTrainingRegistration(100.00);
        $guestPayment = $this->payments->recordManualPayment(null, $this->team, $registration, 100.00, 'EUR', PaymentMethodEnum::CASH, status: PaymentStatusEnum::PENDING);

        $open = $this->payments->openPaymentFor($registration, $registration->user, $this->team);

        $this->assertTrue($open->is($guestPayment));
        $this->assertSame(1, $registration->payments()->count());
    }
}
