<?php

namespace Tests\Feature;

use App\Enums\RegistrationStatusEnum;
use App\Enums\RoleEnum;
use App\Models\Event;
use App\Models\EventOrganization;
use App\Models\EventRegistration;
use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Livewire\Livewire;
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
