<?php

namespace Tests\Feature;

use App\Enums\InvitationStatusEnum;
use App\Enums\PlanTierEnum;
use App\Enums\RoleEnum;
use App\Models\SubscriptionPlan;
use App\Models\Team;
use App\Models\TeamInvitation;
use App\Models\TeamSubscription;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class RegisterTeamTest extends TestCase
{
    use RefreshDatabase;

    public function test_register_team_page_loads(): void
    {
        $response = $this->get('/registracia');

        $response->assertOk();
        $response->assertSeeLivewire('register-team');
    }

    public function test_step1_validates_required_fields(): void
    {
        Livewire::test(\App\Livewire\RegisterTeam::class)
            ->call('nextStep')
            ->assertHasErrors(['firstName', 'lastName', 'email', 'password', 'passwordConfirmation']);
    }

    public function test_step1_validates_email_format(): void
    {
        Livewire::test(\App\Livewire\RegisterTeam::class)
            ->set('firstName', 'Test')
            ->set('lastName', 'User')
            ->set('email', 'not-an-email')
            ->set('password', 'password123')
            ->set('passwordConfirmation', 'password123')
            ->call('nextStep')
            ->assertHasErrors(['email']);
    }

    public function test_step1_validates_unique_email(): void
    {
        User::factory()->create(['email' => 'taken@example.com']);

        Livewire::test(\App\Livewire\RegisterTeam::class)
            ->set('firstName', 'Test')
            ->set('lastName', 'User')
            ->set('email', 'taken@example.com')
            ->set('password', 'password123')
            ->set('passwordConfirmation', 'password123')
            ->call('nextStep')
            ->assertHasErrors(['email']);
    }

    public function test_step1_validates_password_minimum_length(): void
    {
        Livewire::test(\App\Livewire\RegisterTeam::class)
            ->set('firstName', 'Test')
            ->set('lastName', 'User')
            ->set('email', 'test@example.com')
            ->set('password', 'short')
            ->set('passwordConfirmation', 'short')
            ->call('nextStep')
            ->assertHasErrors(['password']);
    }

    public function test_step1_validates_password_confirmation(): void
    {
        Livewire::test(\App\Livewire\RegisterTeam::class)
            ->set('firstName', 'Test')
            ->set('lastName', 'User')
            ->set('email', 'test@example.com')
            ->set('password', 'password123')
            ->set('passwordConfirmation', 'different123')
            ->call('nextStep')
            ->assertHasErrors(['passwordConfirmation']);
    }

    public function test_step1_advances_to_step2_on_valid_data(): void
    {
        Livewire::test(\App\Livewire\RegisterTeam::class)
            ->set('firstName', 'Test')
            ->set('lastName', 'User')
            ->set('email', 'test@example.com')
            ->set('password', 'password123')
            ->set('passwordConfirmation', 'password123')
            ->call('nextStep')
            ->assertSet('step', 2)
            ->assertSet('ownerName', 'Test User')
            ->assertSet('ownerEmail', 'test@example.com');
    }

    public function test_step2_validates_required_fields(): void
    {
        Livewire::test(\App\Livewire\RegisterTeam::class)
            ->set('step', 2)
            ->set('teamName', '')
            ->set('ownerName', '')
            ->set('ownerEmail', '')
            ->call('nextStep')
            ->assertHasErrors(['teamName', 'ownerName', 'ownerEmail']);
    }

    public function test_step2_validates_owner_email_format(): void
    {
        Livewire::test(\App\Livewire\RegisterTeam::class)
            ->set('step', 2)
            ->set('teamName', 'My Team')
            ->set('ownerName', 'Owner')
            ->set('ownerEmail', 'bad-email')
            ->set('country', 'SK')
            ->call('nextStep')
            ->assertHasErrors(['ownerEmail']);
    }

    public function test_step2_validates_logo_must_be_image(): void
    {
        Livewire::test(\App\Livewire\RegisterTeam::class)
            ->set('step', 2)
            ->set('teamName', 'My Team')
            ->set('ownerName', 'Owner')
            ->set('ownerEmail', 'owner@test.com')
            ->set('country', 'SK')
            ->set('logo', UploadedFile::fake()->create('document.csv', 100, 'text/csv'))
            ->call('nextStep')
            ->assertHasErrors(['logo']);
    }

    public function test_step2_advances_to_step3_on_valid_data(): void
    {
        Livewire::test(\App\Livewire\RegisterTeam::class)
            ->set('step', 2)
            ->set('teamName', 'My Team')
            ->set('ownerName', 'Owner Name')
            ->set('ownerEmail', 'owner@test.com')
            ->set('country', 'SK')
            ->call('nextStep')
            ->assertSet('step', 3);
    }

    public function test_previous_step_navigates_back(): void
    {
        Livewire::test(\App\Livewire\RegisterTeam::class)
            ->set('step', 2)
            ->call('previousStep')
            ->assertSet('step', 1);
    }

    public function test_previous_step_does_not_go_below_1(): void
    {
        Livewire::test(\App\Livewire\RegisterTeam::class)
            ->set('step', 1)
            ->call('previousStep')
            ->assertSet('step', 1);
    }

    public function test_select_plan_sets_selected_plan_id(): void
    {
        $plan = SubscriptionPlan::factory()->create();

        Livewire::test(\App\Livewire\RegisterTeam::class)
            ->call('selectPlan', $plan->id)
            ->assertSet('selectedPlanId', $plan->id);
    }

    public function test_billing_period_toggles(): void
    {
        Livewire::test(\App\Livewire\RegisterTeam::class)
            ->assertSet('billingPeriod', 'monthly')
            ->call('toggleBilling')
            ->assertSet('billingPeriod', 'yearly')
            ->call('toggleBilling')
            ->assertSet('billingPeriod', 'monthly');
    }

    public function test_full_registration_creates_user_team_and_subscription(): void
    {
        $plan = SubscriptionPlan::factory()->create([
            'tier' => PlanTierEnum::PRO,
            'is_active' => true,
        ]);

        Livewire::test(\App\Livewire\RegisterTeam::class)
            ->set('firstName', 'Dominik')
            ->set('lastName', 'Klimek')
            ->set('email', 'dominik@bczclub.sk')
            ->set('password', 'password123')
            ->set('passwordConfirmation', 'password123')
            ->call('nextStep')
            ->assertSet('step', 2)
            ->set('teamName', 'BCZ Club')
            ->set('ownerName', 'Dominik Klimek')
            ->set('ownerEmail', 'dominik@bczclub.sk')
            ->set('sportType', 'Street Workout')
            ->set('country', 'SK')
            ->set('city', 'Čadca')
            ->call('nextStep')
            ->assertSet('step', 3)
            ->call('selectPlan', $plan->id)
            ->call('nextStep')
            ->assertSet('step', 4)
            ->assertSet('createdTeamName', 'BCZ Club')
            ->assertSet('createdOwnerName', 'Dominik Klimek');

        $user = User::where('email', 'dominik@bczclub.sk')->first();
        $this->assertNotNull($user);
        $this->assertEquals('Dominik', $user->first_name);
        $this->assertEquals('Klimek', $user->last_name);
        $this->assertNotNull($user->email_verified_at);
        $this->assertTrue($user->hasRole(RoleEnum::ADMIN));

        $team = Team::where('slug', 'bcz-club')->first();
        $this->assertNotNull($team);
        $this->assertEquals('BCZ Club', $team->getTranslation('name', 'sk'));
        $this->assertTrue($team->members()->where('users.id', $user->id)->exists());

        $subscription = TeamSubscription::where('team_id', $team->id)->first();
        $this->assertNotNull($subscription);
        $this->assertEquals('active', $subscription->getRawOriginal('status'));
        $this->assertNotNull($subscription->trial_ends_at);
    }

    public function test_registration_with_logo_upload(): void
    {
        Storage::fake('public');

        $plan = SubscriptionPlan::factory()->create([
            'tier' => PlanTierEnum::PRO,
            'is_active' => true,
        ]);

        Livewire::test(\App\Livewire\RegisterTeam::class)
            ->set('firstName', 'Test')
            ->set('lastName', 'User')
            ->set('email', 'test@example.com')
            ->set('password', 'password123')
            ->set('passwordConfirmation', 'password123')
            ->call('nextStep')
            ->set('teamName', 'Logo Team')
            ->set('ownerName', 'Test User')
            ->set('ownerEmail', 'test@example.com')
            ->set('country', 'SK')
            ->set('logo', UploadedFile::fake()->image('logo.png', 200, 200))
            ->call('nextStep')
            ->call('nextStep')
            ->assertSet('step', 4);

        $team = Team::where('slug', 'logo-team')->first();
        $this->assertNotNull($team);
    }

    public function test_registration_without_selecting_plan_uses_pro_default(): void
    {
        $proPlan = SubscriptionPlan::factory()->create([
            'tier' => PlanTierEnum::PRO,
            'is_active' => true,
        ]);

        Livewire::test(\App\Livewire\RegisterTeam::class)
            ->set('firstName', 'Test')
            ->set('lastName', 'User')
            ->set('email', 'default@example.com')
            ->set('password', 'password123')
            ->set('passwordConfirmation', 'password123')
            ->call('nextStep')
            ->set('teamName', 'Default Plan Team')
            ->set('ownerName', 'Test User')
            ->set('ownerEmail', 'default@example.com')
            ->set('country', 'SK')
            ->call('nextStep')
            ->call('nextStep')
            ->assertSet('step', 4);

        $team = Team::where('slug', 'default-plan-team')->first();
        $subscription = TeamSubscription::where('team_id', $team->id)->first();
        $this->assertNotNull($subscription);
        $this->assertEquals($proPlan->id, $subscription->subscription_plan_id);
    }

    public function test_user_is_authenticated_after_registration(): void
    {
        SubscriptionPlan::factory()->create([
            'tier' => PlanTierEnum::PRO,
            'is_active' => true,
        ]);

        Livewire::test(\App\Livewire\RegisterTeam::class)
            ->set('firstName', 'Auth')
            ->set('lastName', 'Test')
            ->set('email', 'auth@example.com')
            ->set('password', 'password123')
            ->set('passwordConfirmation', 'password123')
            ->call('nextStep')
            ->set('teamName', 'Auth Team')
            ->set('ownerName', 'Auth Test')
            ->set('ownerEmail', 'auth@example.com')
            ->set('country', 'SK')
            ->call('nextStep')
            ->call('nextStep');

        $this->assertAuthenticated();
    }

    public function test_step4_shows_success_with_correct_data(): void
    {
        SubscriptionPlan::factory()->create([
            'tier' => PlanTierEnum::PRO,
            'name' => ['sk' => 'PRO'],
            'is_active' => true,
        ]);

        Livewire::test(\App\Livewire\RegisterTeam::class)
            ->set('firstName', 'Ján')
            ->set('lastName', 'Novák')
            ->set('email', 'jan@novak.sk')
            ->set('password', 'password123')
            ->set('passwordConfirmation', 'password123')
            ->call('nextStep')
            ->set('teamName', 'Novák Team')
            ->set('ownerName', 'Ján Novák')
            ->set('ownerEmail', 'jan@novak.sk')
            ->set('sportType', 'Calisthenics')
            ->set('country', 'SK')
            ->call('nextStep')
            ->call('nextStep')
            ->assertSet('step', 4)
            ->assertSet('createdTeamName', 'Novák Team')
            ->assertSet('createdSportType', 'Calisthenics')
            ->assertSet('createdOwnerName', 'Ján Novák')
            ->assertSet('createdPlanName', 'PRO');
    }

    public function test_pending_invite_code_is_redeemed_after_registration(): void
    {
        $existingTeam = Team::factory()->create(['is_active' => true]);
        $inviter = User::factory()->create();
        $invitation = TeamInvitation::factory()->create([
            'team_id' => $existingTeam->id,
            'code' => 'TESTCODE',
            'status' => InvitationStatusEnum::Pending,
            'invited_by' => $inviter->id,
            'expires_at' => now()->addDays(7),
        ]);

        SubscriptionPlan::factory()->create([
            'tier' => PlanTierEnum::PRO,
            'is_active' => true,
        ]);

        Livewire::test(\App\Livewire\RegisterTeam::class)
            ->set('firstName', 'Invited')
            ->set('lastName', 'User')
            ->set('email', 'invited@example.com')
            ->set('password', 'password123')
            ->set('passwordConfirmation', 'password123')
            ->call('nextStep')
            ->set('teamName', 'Invited Team')
            ->set('ownerName', 'Invited User')
            ->set('ownerEmail', 'invited@example.com')
            ->set('country', 'SK')
            ->call('nextStep')
            ->call('nextStep')
            ->assertSet('step', 4);

        // Without session code, user should NOT be added to existing team
        $user = User::where('email', 'invited@example.com')->first();
        $this->assertFalse($existingTeam->members()->where('users.id', $user->id)->exists());
    }

    public function test_pending_invite_code_in_session_is_redeemed_after_registration(): void
    {
        $existingTeam = Team::factory()->create(['is_active' => true]);
        $inviter = User::factory()->create();
        $invitation = TeamInvitation::factory()->create([
            'team_id' => $existingTeam->id,
            'code' => 'JOIN1234',
            'status' => InvitationStatusEnum::Pending,
            'invited_by' => $inviter->id,
            'expires_at' => now()->addDays(7),
        ]);

        SubscriptionPlan::factory()->create([
            'tier' => PlanTierEnum::PRO,
            'is_active' => true,
        ]);

        session(['pending_invite_code' => 'JOIN1234']);

        Livewire::test(\App\Livewire\RegisterTeam::class)
            ->set('firstName', 'Joined')
            ->set('lastName', 'User')
            ->set('email', 'joined@example.com')
            ->set('password', 'password123')
            ->set('passwordConfirmation', 'password123')
            ->call('nextStep')
            ->set('teamName', 'New Team')
            ->set('ownerName', 'Joined User')
            ->set('ownerEmail', 'joined@example.com')
            ->set('country', 'SK')
            ->call('nextStep')
            ->call('nextStep')
            ->assertSet('step', 4);

        $user = User::where('email', 'joined@example.com')->first();
        $this->assertTrue($existingTeam->members()->where('users.id', $user->id)->exists());

        $invitation->refresh();
        $this->assertEquals(InvitationStatusEnum::Accepted, $invitation->status);
        $this->assertNotNull($invitation->accepted_at);

        // Session should be cleared
        $this->assertNull(session('pending_invite_code'));
    }

    public function test_localized_route_works(): void
    {
        $response = $this->get('/en/registracia');

        $response->assertOk();
    }

    public function test_join_team_localized_route_works(): void
    {
        $response = $this->get('/cs/pridaj-sa');

        $response->assertOk();
    }
}
