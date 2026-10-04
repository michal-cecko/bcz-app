<?php

namespace Tests\Feature;

use App\Enums\EventPricingTypeEnum;
use App\Enums\PaymentMethodEnum;
use App\Enums\PaymentStatusEnum;
use App\Enums\RegistrationStatusEnum;
use App\Enums\RoleEnum;
use App\Models\Event;
use App\Models\EventOrganization;
use App\Models\EventRegistration;
use App\Models\PaymentMethod;
use App\Models\Team;
use App\Models\User;
use App\Services\GoPayService;
use App\Services\PaymentService;
use GoPay\Http\Response as GoPayResponse;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Livewire\Features\SupportLockedProperties\CannotUpdateLockedPropertyException;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\DataProvider;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class EventRegistrationFlowTest extends TestCase
{
    use RefreshDatabase;

    protected Team $team;

    protected function setUp(): void
    {
        parent::setUp();

        foreach (RoleEnum::cases() as $role) {
            Role::firstOrCreate(['name' => $role->value, 'guard_name' => 'web']);
        }

        $this->team = Team::factory()->create();
    }

    /**
     * Create a published, currently-registering organized event with a basic
     * registration form schema (first name, last name, email, phone).
     */
    protected function createRegisteringEvent(): Event
    {
        $event = Event::factory()->organized()->create([
            'team_id' => $this->team->id,
            'date' => now()->addMonth(),
            'date_end' => null,
        ]);

        EventOrganization::factory()
            ->withRegistrationWindow()
            ->create([
                'event_id' => $event->id,
                'registration_form_schema' => [
                    ['label' => ['sk' => 'Meno'], 'name' => 'meno', 'type' => 'first_name', 'width' => 'half', 'required' => true, 'has_condition' => false],
                    ['label' => ['sk' => 'Priezvisko'], 'name' => 'priezvisko', 'type' => 'last_name', 'width' => 'half', 'required' => true, 'has_condition' => false],
                    ['label' => ['sk' => 'Email'], 'name' => 'email', 'type' => 'email', 'width' => 'full', 'required' => true, 'has_condition' => false],
                    ['label' => ['sk' => 'Telefón'], 'name' => 'telefon', 'type' => 'phone', 'width' => 'full', 'required' => true, 'has_condition' => false],
                ],
            ]);

        return $event->refresh();
    }

    /**
     * Same as createRegisteringEvent(), with an extra checkbox field appended.
     */
    protected function createRegisteringEventWithCheckbox(bool $checkboxRequired): Event
    {
        $event = Event::factory()->organized()->create([
            'team_id' => $this->team->id,
            'date' => now()->addMonth(),
            'date_end' => null,
        ]);

        EventOrganization::factory()
            ->withRegistrationWindow()
            ->create([
                'event_id' => $event->id,
                'registration_form_schema' => [
                    ['label' => ['sk' => 'Meno'], 'name' => 'meno', 'type' => 'first_name', 'width' => 'half', 'required' => true, 'has_condition' => false],
                    ['label' => ['sk' => 'Priezvisko'], 'name' => 'priezvisko', 'type' => 'last_name', 'width' => 'half', 'required' => true, 'has_condition' => false],
                    ['label' => ['sk' => 'Email'], 'name' => 'email', 'type' => 'email', 'width' => 'full', 'required' => true, 'has_condition' => false],
                    ['label' => ['sk' => 'Telefón'], 'name' => 'telefon', 'type' => 'phone', 'width' => 'full', 'required' => true, 'has_condition' => false],
                    ['label' => ['sk' => 'Súhlas'], 'placeholder' => ['sk' => 'Súhlasím s pravidlami'], 'name' => 'suhlas', 'type' => 'checkbox', 'width' => 'full', 'required' => $checkboxRequired, 'has_condition' => false],
                ],
            ]);

        return $event->refresh();
    }

    public function test_required_checkbox_blocks_submit_when_unchecked(): void
    {
        Mail::fake();

        $event = $this->createRegisteringEventWithCheckbox(checkboxRequired: true);

        Livewire::test('event-registration-form', ['event' => $event])
            ->set('fields.meno', 'New')
            ->set('fields.priezvisko', 'Guest')
            ->set('fields.email', 'cbx-required@test.com')
            ->set('fields.telefon', '+421900111222')
            ->set('fields.suhlas', false)
            ->set('gdprAgreed', true)
            ->call('submit')
            ->assertHasErrors(['fields.suhlas']);

        $this->assertNull(User::where('email', 'cbx-required@test.com')->first());
    }

    public function test_checked_checkbox_is_stored_as_field_value(): void
    {
        Mail::fake();

        $event = $this->createRegisteringEventWithCheckbox(checkboxRequired: true);

        Livewire::test('event-registration-form', ['event' => $event])
            ->set('fields.meno', 'New')
            ->set('fields.priezvisko', 'Guest')
            ->set('fields.email', 'cbx-checked@test.com')
            ->set('fields.telefon', '+421900333444')
            ->set('fields.suhlas', true)
            ->set('gdprAgreed', true)
            ->call('submit')
            ->assertHasNoErrors();

        $user = User::where('email', 'cbx-checked@test.com')->first();
        $this->assertNotNull($user);

        $registration = EventRegistration::where('event_id', $event->id)
            ->where('user_id', $user->id)
            ->first();
        $this->assertNotNull($registration);

        $this->assertSame('1', $registration->fieldValues()->where('field_key', 'suhlas')->value('value'));
    }

    public function test_optional_checkbox_left_unchecked_submits_and_is_not_stored(): void
    {
        Mail::fake();

        $event = $this->createRegisteringEventWithCheckbox(checkboxRequired: false);

        Livewire::test('event-registration-form', ['event' => $event])
            ->set('fields.meno', 'New')
            ->set('fields.priezvisko', 'Guest')
            ->set('fields.email', 'cbx-optional@test.com')
            ->set('fields.telefon', '+421900555666')
            ->set('fields.suhlas', false)
            ->set('gdprAgreed', true)
            ->call('submit')
            ->assertHasNoErrors();

        $user = User::where('email', 'cbx-optional@test.com')->first();
        $registration = EventRegistration::where('event_id', $event->id)
            ->where('user_id', $user->id)
            ->first();
        $this->assertNotNull($registration);

        // An unchecked checkbox is normalized to an empty string and therefore not persisted.
        $this->assertFalse($registration->fieldValues()->where('field_key', 'suhlas')->exists());
    }

    public function test_new_guest_user_is_not_attached_to_any_team(): void
    {
        Mail::fake();

        $event = $this->createRegisteringEvent();

        Livewire::test('event-registration-form', ['event' => $event])
            ->set('fields.meno', 'New')
            ->set('fields.priezvisko', 'Guest')
            ->set('fields.email', 'newguest@test.com')
            ->set('fields.telefon', '+421900654321')
            ->set('gdprAgreed', true)
            ->call('submit');

        $user = User::where('email', 'newguest@test.com')->first();

        $this->assertNotNull($user);
        // The new account is created but intentionally left without any team.
        $this->assertSame(0, $user->teams()->count());
        // It still receives the global CUSTOMER role.
        $this->assertTrue($user->hasRole(RoleEnum::CUSTOMER->value));

        // The registration itself is still recorded against the user.
        $registration = EventRegistration::where('event_id', $event->id)
            ->where('user_id', $user->id)
            ->first();

        $this->assertNotNull($registration);
        $this->assertEquals(RegistrationStatusEnum::Approved, $registration->status);
    }

    #[DataProvider('serverOwnedProperties')]
    public function test_server_owned_state_cannot_be_set_from_the_client(string $property, string $value): void
    {
        $event = $this->createRegisteringEvent();

        $this->expectException(CannotUpdateLockedPropertyException::class);

        Livewire::test('event-registration-form', ['event' => $event])
            ->set($property, $value);
    }

    /**
     * @return array<string, array{string, string}>
     */
    public static function serverOwnedProperties(): array
    {
        return [
            'pending payment id' => ['pendingPaymentId', '0199a6f0-0000-7000-8000-000000000000'],
            'registration state' => ['registrationState', 'payment_needed'],
        ];
    }

    public function test_returning_user_with_an_unpaid_registration_gets_the_payment_box_back(): void
    {
        $event = $this->createRegisteringEvent();
        $event->organization->update(['pricing_type' => EventPricingTypeEnum::Paid, 'price_amount' => 25.00]);

        $user = User::factory()->create();
        $registration = EventRegistration::factory()->create([
            'event_id' => $event->id,
            'user_id' => $user->id,
            'status' => RegistrationStatusEnum::Pending,
        ]);
        $payment = app(PaymentService::class)->createPendingPayment($user, $this->team, $registration, 25.00);

        Livewire::actingAs($user)
            ->test('event-registration-form', ['event' => $event->refresh()])
            ->assertSet('registrationState', 'payment_needed')
            ->assertSet('pendingPaymentId', $payment->id);
    }

    public function test_returning_user_with_a_paid_registration_sees_already_registered(): void
    {
        $event = $this->createRegisteringEvent();
        $event->organization->update(['pricing_type' => EventPricingTypeEnum::Paid, 'price_amount' => 25.00]);

        $user = User::factory()->create();
        $registration = EventRegistration::factory()->approved()->create([
            'event_id' => $event->id,
            'user_id' => $user->id,
        ]);
        $payment = app(PaymentService::class)->createPendingPayment($user, $this->team, $registration, 25.00);
        $payment->update(['status' => PaymentStatusEnum::COMPLETED, 'paid_at' => now()]);

        Livewire::actingAs($user)
            ->test('event-registration-form', ['event' => $event->refresh()])
            ->assertSet('registrationState', 'already_registered');
    }

    public function test_gopay_button_works_for_a_guest_who_has_just_registered(): void
    {
        Mail::fake();

        $event = $this->createRegisteringEvent();
        $event->organization->update(['pricing_type' => EventPricingTypeEnum::Paid, 'price_amount' => 25.00]);

        $gopay = PaymentMethod::create([
            'method' => PaymentMethodEnum::GOPAY,
            'title' => ['sk' => 'GoPay'],
            'is_active' => true,
            'sort_order' => 0,
        ]);
        $event->paymentMethods()->attach($gopay->id, ['is_enabled' => true, 'sort_order' => 0]);

        $response = new GoPayResponse;
        $response->statusCode = 200;
        $response->json = ['id' => 4001, 'order_number' => 'EVE-1', 'gw_url' => 'https://gate.gopay.test/4001'];

        $this->mock(GoPayService::class)
            ->shouldReceive('createPayment')
            ->once()
            ->withArgs(fn (array $params): bool => $params['amount'] === 2500 && $params['payer_email'] === 'guest.payer@test.com')
            ->andReturn($response);

        Livewire::test('event-registration-form', ['event' => $event->refresh()])
            ->set('fields.meno', 'Guest')
            ->set('fields.priezvisko', 'Payer')
            ->set('fields.email', 'guest.payer@test.com')
            ->set('fields.telefon', '+421900777888')
            ->set('gdprAgreed', true)
            ->call('submit')
            ->assertSet('registrationState', 'payment_needed')
            ->assertSet('selectedPaymentMethod', PaymentMethodEnum::GOPAY->value)
            ->call('handlePayment')
            ->assertRedirect('https://gate.gopay.test/4001');
    }

    public function test_same_email_cannot_register_twice_for_one_event(): void
    {
        Mail::fake();

        $event = $this->createRegisteringEvent();

        // First registration succeeds.
        Livewire::test('event-registration-form', ['event' => $event])
            ->set('fields.meno', 'Samuel')
            ->set('fields.priezvisko', 'Ivan')
            ->set('fields.email', 'coach@test.com')
            ->set('fields.telefon', '+421900111000')
            ->set('gdprAgreed', true)
            ->call('submit')
            ->assertHasNoErrors();

        $this->assertSame(1, EventRegistration::where('event_id', $event->id)->count());

        // A second registration under the SAME email (different casing, different athlete) is blocked.
        Livewire::test('event-registration-form', ['event' => $event])
            ->set('fields.meno', 'Simon')
            ->set('fields.priezvisko', 'Toráč')
            ->set('fields.email', 'COACH@test.com')
            ->set('fields.telefon', '+421900222000')
            ->set('gdprAgreed', true)
            ->call('submit')
            ->assertHasErrors(['fields.email']);

        $this->assertSame(1, EventRegistration::where('event_id', $event->id)->count());
    }
}
