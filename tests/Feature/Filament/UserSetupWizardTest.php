<?php

namespace Tests\Feature\Filament;

use App\Enums\GenderEnum;
use App\Enums\RoleEnum;
use App\Filament\Pages\Auth\UserSetupWizard;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class UserSetupWizardTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Filament::setCurrentPanel(Filament::getPanel('customer'));

        Role::firstOrCreate(['name' => RoleEnum::CUSTOMER->value, 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => RoleEnum::ATHLETE->value, 'guard_name' => 'web']);
    }

    private function newCustomer(): User
    {
        $user = User::factory()->create(['password_set_at' => null]);
        $user->assignRole(RoleEnum::CUSTOMER->value);

        return $user;
    }

    private function existingCustomer(): User
    {
        $user = User::factory()->create([
            'password_set_at' => now(),
            'password' => Hash::make('ExistingPass1!'),
        ]);
        $user->assignRole(RoleEnum::CUSTOMER->value);

        return $user;
    }

    // -------------------------------------------------------------------------
    // Access control
    // -------------------------------------------------------------------------

    public function test_unauthenticated_user_cannot_access_wizard(): void
    {
        $this->assertFalse(UserSetupWizard::canAccess());
    }

    public function test_customer_can_access_wizard(): void
    {
        $this->actingAs($this->newCustomer());

        $this->assertTrue(UserSetupWizard::canAccess());
    }

    public function test_user_without_member_role_cannot_access_wizard(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $this->assertFalse(UserSetupWizard::canAccess());
    }

    // -------------------------------------------------------------------------
    // Mount / initial state
    // -------------------------------------------------------------------------

    public function test_new_user_sees_password_step_first(): void
    {
        Livewire::actingAs($this->newCustomer());

        Livewire::test(UserSetupWizard::class)
            ->assertSet('isRevisit', false)
            ->assertWizardCurrentStep(1);
    }

    public function test_returning_user_skips_password_step(): void
    {
        Livewire::actingAs($this->existingCustomer());

        Livewire::test(UserSetupWizard::class)
            ->assertSet('isRevisit', true)
            ->assertWizardCurrentStep(1);
    }

    public function test_mount_prefills_existing_profile_data(): void
    {
        $user = User::factory()->create([
            'password_set_at' => now(),
            'password' => Hash::make('Pass1!'),
            'phone' => '+421900000000',
            'gender' => GenderEnum::MALE,
            'locale' => 'en',
        ]);
        $user->assignRole(RoleEnum::CUSTOMER->value);

        Livewire::actingAs($user);

        Livewire::test(UserSetupWizard::class)
            ->assertSet('data.phone', '+421900000000')
            ->assertSet('data.gender', GenderEnum::MALE->value)
            ->assertSet('data.locale', 'en');
    }

    // -------------------------------------------------------------------------
    // Password step — validation errors stay on step 1
    // -------------------------------------------------------------------------

    public function test_password_step_requires_password(): void
    {
        Livewire::actingAs($this->newCustomer());

        Livewire::test(UserSetupWizard::class)
            ->set('data.password', '')
            ->set('data.passwordConfirmation', '')
            ->goToNextWizardStep()
            ->assertHasFormErrors(['password' => 'required']);
    }

    public function test_password_step_requires_confirmation(): void
    {
        Livewire::actingAs($this->newCustomer());

        Livewire::test(UserSetupWizard::class)
            ->set('data.password', 'Password123!')
            ->set('data.passwordConfirmation', '')
            ->goToNextWizardStep()
            ->assertHasFormErrors(['passwordConfirmation' => 'required']);
    }

    public function test_password_step_requires_matching_confirmation(): void
    {
        Livewire::actingAs($this->newCustomer());

        Livewire::test(UserSetupWizard::class)
            ->set('data.password', 'Password123!')
            ->set('data.passwordConfirmation', 'Different123!')
            ->goToNextWizardStep()
            ->assertHasFormErrors(['password']);
    }

    public function test_password_step_enforces_password_strength(): void
    {
        Livewire::actingAs($this->newCustomer());

        Livewire::test(UserSetupWizard::class)
            ->set('data.password', 'weak')
            ->set('data.passwordConfirmation', 'weak')
            ->goToNextWizardStep()
            ->assertHasFormErrors(['password']);
    }

    // -------------------------------------------------------------------------
    // Password step — successful advancement
    // -------------------------------------------------------------------------

    public function test_password_step_advances_to_step_2_on_valid_input(): void
    {
        Livewire::actingAs($this->newCustomer());

        Livewire::test(UserSetupWizard::class)
            ->set('data.password', 'Password123!')
            ->set('data.passwordConfirmation', 'Password123!')
            ->goToNextWizardStep()
            ->assertHasNoFormErrors()
            ->assertWizardCurrentStep(2);
    }

    public function test_password_is_correctly_hashed_after_advancing_step_1(): void
    {
        $user = $this->newCustomer();

        Livewire::actingAs($user);

        Livewire::test(UserSetupWizard::class)
            ->set('data.password', 'Password123!')
            ->set('data.passwordConfirmation', 'Password123!')
            ->goToNextWizardStep();

        $this->assertTrue(Hash::check('Password123!', $user->fresh()->password));
    }

    public function test_password_set_at_is_stamped_after_advancing_step_1(): void
    {
        $user = $this->newCustomer();

        Livewire::actingAs($user);

        Livewire::test(UserSetupWizard::class)
            ->set('data.password', 'Password123!')
            ->set('data.passwordConfirmation', 'Password123!')
            ->goToNextWizardStep();

        $this->assertNotNull($user->fresh()->password_set_at);
    }

    // -------------------------------------------------------------------------
    // Personal info step
    // -------------------------------------------------------------------------

    public function test_personal_info_step_advances_with_optional_fields_empty(): void
    {
        Livewire::actingAs($this->newCustomer());

        Livewire::test(UserSetupWizard::class)
            ->set('data.password', 'Password123!')
            ->set('data.passwordConfirmation', 'Password123!')
            ->goToNextWizardStep()           // → step 2 personal info
            ->assertWizardCurrentStep(2)
            ->goToNextWizardStep()           // → step 3 profile
            ->assertHasNoFormErrors()
            ->assertWizardCurrentStep(3);
    }

    // -------------------------------------------------------------------------
    // Profile step
    // -------------------------------------------------------------------------

    public function test_profile_step_advances_with_locale_set(): void
    {
        Livewire::actingAs($this->newCustomer());

        Livewire::test(UserSetupWizard::class)
            ->set('data.password', 'Password123!')
            ->set('data.passwordConfirmation', 'Password123!')
            ->goToNextWizardStep()  // step 2
            ->goToNextWizardStep()  // step 3
            ->set('data.locale', 'en')
            ->goToNextWizardStep()
            ->assertHasNoFormErrors();
    }

    // -------------------------------------------------------------------------
    // Final save
    // -------------------------------------------------------------------------

    public function test_save_persists_personal_info(): void
    {
        $user = $this->newCustomer();

        Livewire::actingAs($user);

        Livewire::test(UserSetupWizard::class)
            ->set('data.password', 'Password123!')
            ->set('data.passwordConfirmation', 'Password123!')
            ->set('data.phone', '+421900123456')
            ->set('data.gender', GenderEnum::MALE->value)
            ->set('data.locale', 'cs')
            ->call('save');

        $fresh = $user->fresh();
        $this->assertSame('+421900123456', $fresh->phone);
        $this->assertEquals(GenderEnum::MALE, $fresh->gender);
        $this->assertSame('cs', $fresh->locale);
    }

    public function test_save_redirects_to_customer_panel(): void
    {
        Livewire::actingAs($this->newCustomer());

        Livewire::test(UserSetupWizard::class)
            ->set('data.password', 'Password123!')
            ->set('data.passwordConfirmation', 'Password123!')
            ->set('data.locale', 'sk')
            ->call('save')
            ->assertRedirect('/customer');
    }

    public function test_save_sends_success_notification(): void
    {
        Livewire::actingAs($this->newCustomer());

        Livewire::test(UserSetupWizard::class)
            ->set('data.password', 'Password123!')
            ->set('data.passwordConfirmation', 'Password123!')
            ->set('data.locale', 'sk')
            ->call('save')
            ->assertNotified();
    }

    // -------------------------------------------------------------------------
    // Revisit (password already set)
    // -------------------------------------------------------------------------

    public function test_revisit_user_can_update_personal_info(): void
    {
        $user = $this->existingCustomer();

        Livewire::actingAs($user);

        Livewire::test(UserSetupWizard::class)
            ->assertSet('isRevisit', true)
            ->set('data.phone', '+421911000000')
            ->set('data.locale', 'en')
            ->call('save');

        $fresh = $user->fresh();
        $this->assertSame('+421911000000', $fresh->phone);
        $this->assertSame('en', $fresh->locale);
    }

    public function test_revisit_user_password_is_unchanged_by_save(): void
    {
        $user = $this->existingCustomer();
        $originalHash = $user->password;

        Livewire::actingAs($user);

        Livewire::test(UserSetupWizard::class)
            ->set('data.locale', 'sk')
            ->call('save');

        $this->assertSame($originalHash, $user->fresh()->password);
    }
}
