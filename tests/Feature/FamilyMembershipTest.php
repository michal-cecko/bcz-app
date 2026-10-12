<?php

namespace Tests\Feature;

use App\Enums\MembershipStatusEnum;
use App\Enums\PaymentStatusEnum;
use App\Enums\RegistrationStatusEnum;
use App\Enums\RoleEnum;
use App\Enums\TeamJoinModeEnum;
use App\Enums\TrainingPricingTypeEnum;
use App\Filament\Pages\MemberMembership;
use App\Filament\Pages\MyFamily;
use App\Livewire\JoinDefaultTeam;
use App\Livewire\JoinTeam;
use App\Models\Member;
use App\Models\Membership;
use App\Models\Payment;
use App\Models\Setting;
use App\Models\Team;
use App\Models\TeamJoinRequest;
use App\Models\TeamSeason;
use App\Models\Training;
use App\Models\TrainingRegistration;
use App\Models\TrainingWaitlist;
use App\Models\User;
use App\Notifications\FamilyLoginLink;
use App\Services\PaymentService;
use App\Services\RegistrationService;
use App\Services\SeasonService;
use Filament\Actions\Testing\TestAction;
use Filament\Facades\Filament;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\URL;
use Illuminate\Validation\ValidationException;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Tests\TestCase;

class FamilyMembershipTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        config(['family.enabled' => true]);
        Mail::fake();
        Notification::fake();
        foreach (RoleEnum::cases() as $role) {
            Role::firstOrCreate(['name' => $role->value, 'guard_name' => 'web']);
        }
        Filament::setCurrentPanel(Filament::getPanel('customer'));
    }

    public function test_siblings_take_separate_places_and_only_the_paid_child_is_approved(): void
    {
        $parent = User::factory()->create();
        $children = Member::factory()->count(2)->create(['user_id' => $parent->id]);
        $season = TeamSeason::factory()->create(['fee_amount' => 100, 'prorate_fee' => false]);
        $training = Training::factory()->create(['team_id' => $season->team_id, 'team_season_id' => $season->id,
            'pricing_type' => TrainingPricingTypeEnum::MEMBERSHIP_REQUIRED, 'max_capacity' => 2]);
        $registrations = $children->map(fn ($child) => RegistrationService::registerTraining($training, $parent, $child, []));
        $this->assertSame(2, $training->spotHoldingRegistrations()->count());
        $this->assertSame(2, Membership::count());
        $this->assertSame(1, User::count());
        $this->assertSame(0, $parent->teams()->count());

        $membership = $children[0]->memberships()->firstOrFail();
        $payment = $membership->payments()->firstOrFail();
        $payment->update(['status' => PaymentStatusEnum::COMPLETED]);
        app(PaymentService::class)->processPaymentCompleted($payment, false);
        $this->assertSame(RegistrationStatusEnum::Approved, $registrations[0]->fresh()->status);
        $this->assertSame(RegistrationStatusEnum::Pending, $registrations[1]->fresh()->status);
        $this->assertSame(MembershipStatusEnum::PENDING, $children[1]->memberships()->first()->status);
    }

    public function test_free_flag_and_parent_staff_role_do_not_apply_to_a_sibling(): void
    {
        $parent = User::factory()->create(['has_free_membership' => true]);
        $parent->assignRole(RoleEnum::ADMIN);
        $team = Team::factory()->create();
        $free = Member::factory()->create(['user_id' => $parent->id, 'has_free_membership' => true]);
        $paid = Member::factory()->create(['user_id' => $parent->id]);
        $free->enroll($team);
        $paid->enroll($team);
        $season = app(SeasonService::class)->createSeasonWithMemberships($team, [
            'name' => 'Family season', 'starts_at' => today(), 'ends_at' => today()->addMonths(3),
            'fee_amount' => 100, 'fee_currency' => 'EUR', 'prorate_fee' => false, 'payment_deadline_days' => 14,
        ]);
        $this->assertSame(2, $season->memberships()->count());
        $this->assertSame(MembershipStatusEnum::ACTIVE, $free->memberships()->first()->status);
        $this->assertSame(MembershipStatusEnum::PENDING, $paid->memberships()->first()->status);
        $this->assertEquals(100, $paid->memberships()->first()->fee_amount);
    }

    public function test_repeated_payment_request_reuses_the_same_fee_and_payment(): void
    {
        $child = Member::factory()->create();
        $season = TeamSeason::factory()->create();
        $child->enroll($season->team);
        $service = app(PaymentService::class);
        $first = $service->ensurePendingMembershipPayment($child, $season->team, $season);
        $second = $service->ensurePendingMembershipPayment($child, $season->team, $season);
        $this->assertSame($first->id, $second->id);
        $this->assertSame(1, Membership::count());
        $this->assertSame(1, Payment::count());
    }

    public function test_another_parent_cannot_register_or_edit_a_child(): void
    {
        $child = Member::factory()->create();
        $other = User::factory()->create();
        $this->assertFalse(Gate::forUser($other)->allows('update', $child));
        $this->expectException(HttpException::class);
        RegistrationService::registerTraining(Training::factory()->free()->create(), $other, $child, []);
    }

    public function test_duplicate_registration_is_rejected_but_a_cancelled_place_can_be_rebooked(): void
    {
        $child = Member::factory()->create();
        $training = Training::factory()->free()->create();
        $registration = RegistrationService::registerTraining($training, $child->user, $child, []);
        try {
            RegistrationService::registerTraining($training, $child->user, $child, []);
            $this->fail('Duplicate was accepted.');
        } catch (ValidationException $exception) {
            $this->assertArrayHasKey('memberId', $exception->errors());
        }
        $registration->update(['status' => RegistrationStatusEnum::Cancelled]);
        RegistrationService::registerTraining($training, $child->user, $child, []);
        $this->assertSame(2, TrainingRegistration::count());
    }

    public function test_capacity_is_rechecked_on_submit(): void
    {
        $children = Member::factory()->count(2)->create();
        $training = Training::factory()->free()->create(['max_capacity' => 1]);
        RegistrationService::registerTraining($training, $children[0]->user, $children[0], []);
        $this->expectException(ValidationException::class);
        RegistrationService::registerTraining($training, $children[1]->user, $children[1], []);
    }

    public function test_family_page_creates_a_profile_without_a_child_login_or_fee(): void
    {
        $parent = User::factory()->create(['is_parent' => true]);
        $parent->assignRole(RoleEnum::CUSTOMER);
        Livewire::actingAs($parent)->test(MyFamily::class)
            ->callAction(TestAction::make('addMember')->table(), [
                'first_name' => 'Anna', 'last_name' => 'Child', 'birth_date' => '2018-01-01',
            ])->assertHasNoActionErrors();
        $this->assertDatabaseHas('members', ['user_id' => $parent->id, 'name' => 'Anna Child']);
        $this->assertSame(1, User::count());
        $this->assertSame(0, Membership::count());
    }

    public function test_changing_child_resets_registration_state_and_prefills_child_details(): void
    {
        $parent = User::factory()->create();
        $children = Member::factory()->count(2)->create(['user_id' => $parent->id]);
        $training = Training::factory()->free()->create();
        RegistrationService::registerTraining($training, $parent, $children[0], []);
        Livewire::actingAs($parent)->test('training-registration-form', ['training' => $training])
            ->set('memberId', $children[0]->id)->assertSet('registrationState', 'already_registered')
            ->set('memberId', $children[1]->id)->assertSet('registrationState', 'form')
            ->assertSet('registrationId', null)->assertSet('fields.meno', $children[1]->first_name)
            ->assertSet('fields.email', $parent->email);
    }

    public function test_parent_gets_team_access_without_staff_permissions(): void
    {
        $child = Member::factory()->create();
        $team = Team::factory()->create();
        $child->enroll($team);
        $this->assertTrue($child->user->canAccessTenant($team));
        $this->assertSame('admin', $child->user->homePanelId());
        $this->assertFalse($child->user->hasTeamRole(RoleEnum::COACH, $team));
    }

    public function test_a_guest_must_verify_account_access_before_seeing_children(): void
    {
        $child = Member::factory()->create();
        $training = Training::factory()->free()->create();
        Livewire::test('training-registration-form', ['training' => $training])
            ->assertDontSee($child->name)
            ->set('contactName', 'Someone')->set('contactEmail', $child->user->email)
            ->call('requestFamilyLogin')->assertSet('registrationState', 'login_sent');
        Notification::assertSentTo($child->user, FamilyLoginLink::class);
        $this->assertSame(0, TrainingRegistration::count());
        $this->assertSame(1, User::count());
        $this->assertGuest();
    }

    public function test_new_guest_contact_is_separate_from_a_child_and_verified_by_the_signed_link(): void
    {
        $training = Training::factory()->free()->create();
        Livewire::test('training-registration-form', ['training' => $training])
            ->set('contactName', 'Parent One')->set('contactEmail', 'family@example.test')
            ->call('requestFamilyLogin')->assertSet('registrationState', 'login_sent');
        $parent = User::where('email', 'family@example.test')->firstOrFail();
        $this->assertSame('Parent One', $parent->name);
        $this->assertNull($parent->email_verified_at);
        $this->assertSame(0, Member::count());
        $returnPath = route('team.training.show', [$training->team, $training], absolute: false);
        Notification::assertSentTo($parent, FamilyLoginLink::class,
            fn (FamilyLoginLink $notification): bool => $notification->returnPath === $returnPath);
        $url = URL::temporarySignedRoute('magic-login', now()->addMinutes(30), [
            'user' => $parent->id, 'return' => $returnPath,
        ]);
        $this->get($url)->assertRedirect($returnPath);
        $this->get($returnPath)->assertOk();
        $this->assertAuthenticatedAs($parent);
        $this->assertNotNull($parent->fresh()->email_verified_at);
    }

    public function test_a_forged_child_selection_is_rejected_on_the_training_form(): void
    {
        $parent = User::factory()->create();
        $other = Member::factory()->create();
        $this->expectException(ModelNotFoundException::class);
        Livewire::actingAs($parent)->test('training-registration-form', ['training' => Training::factory()->free()->create()])
            ->set('memberId', $other->id);
        $this->assertSame(0, TrainingRegistration::count());
    }

    public function test_member_details_and_free_flags_are_hidden_from_another_family(): void
    {
        $child = Member::factory()->create();
        $other = User::factory()->create(['is_parent' => true]);
        $other->assignRole(RoleEnum::CUSTOMER);
        Livewire::actingAs($other)->test(MyFamily::class)->assertCanNotSeeTableRecords([$child]);
        $this->assertFalse(Gate::forUser($other)->allows('view', $child));
    }

    public function test_renewal_change_only_updates_the_selected_child(): void
    {
        $parent = User::factory()->create();
        $children = Member::factory()->count(2)->create(['user_id' => $parent->id]);
        $team = Team::factory()->create();
        foreach ($children as $child) {
            $child->enroll($team);
        }
        Filament::setCurrentPanel(Filament::getPanel('admin'));
        $this->actingAs($parent);
        Filament::setTenant($team);
        Livewire::actingAs($parent)->test(MemberMembership::class)->set('memberId', $children[0]->id)
            ->call('requestDisableContinuousMembership')->call('confirmDisableContinuousMembership');
        $this->assertFalse($children[0]->teams()->first()->pivot->continuous_membership);
        $this->assertTrue($children[1]->teams()->first()->pivot->continuous_membership);
    }

    public function test_registration_removes_only_that_child_from_the_waitlist(): void
    {
        $parent = User::factory()->create();
        $children = Member::factory()->count(2)->create(['user_id' => $parent->id]);
        $training = Training::factory()->free()->create();
        foreach ($children as $child) {
            TrainingWaitlist::create(['training_id' => $training->id, 'member_id' => $child->id, 'user_id' => $parent->id]);
        }
        RegistrationService::registerTraining($training, $parent, $children[0], []);
        $this->assertDatabaseMissing('training_waitlist', ['member_id' => $children[0]->id]);
        $this->assertDatabaseHas('training_waitlist', ['member_id' => $children[1]->id]);
    }

    public function test_one_paid_season_does_not_approve_a_future_season(): void
    {
        $child = Member::factory()->create();
        $current = TeamSeason::factory()->create();
        $future = TeamSeason::factory()->future()->create(['team_id' => $current->team_id]);
        $currentTraining = Training::factory()->create(['team_id' => $current->team_id, 'team_season_id' => $current->id]);
        $futureTraining = Training::factory()->create(['team_id' => $current->team_id, 'team_season_id' => $future->id]);
        $currentTraining->update(['pricing_type' => TrainingPricingTypeEnum::MEMBERSHIP_REQUIRED]);
        $futureTraining->update(['pricing_type' => TrainingPricingTypeEnum::MEMBERSHIP_REQUIRED]);
        $first = RegistrationService::registerTraining($currentTraining, $child->user, $child, []);
        $second = RegistrationService::registerTraining($futureTraining, $child->user, $child, []);
        $payment = $child->memberships()->where('team_season_id', $current->id)->firstOrFail()->payments()->firstOrFail();
        $payment->update(['status' => PaymentStatusEnum::COMPLETED]);
        app(PaymentService::class)->processPaymentCompleted($payment, false);
        $this->assertSame(RegistrationStatusEnum::Approved, $first->fresh()->status);
        $this->assertSame(RegistrationStatusEnum::Pending, $second->fresh()->status);
    }

    public function test_a_parent_can_join_a_team_for_each_child_without_becoming_an_athlete(): void
    {
        $parent = User::factory()->create();
        $children = Member::factory()->count(2)->create(['user_id' => $parent->id]);
        $team = Team::factory()->create(['join_mode' => TeamJoinModeEnum::OPEN]);
        foreach ($children as $child) {
            Livewire::actingAs($parent)->test(JoinTeam::class)
                ->set('memberId', $child->id)->call('selectTeam', $team->id)->assertSet('joinedDirectly', true);
        }
        $this->assertSame(2, $team->enrolledMembers()->count());
        $this->assertSame(0, $parent->teams()->count());
    }

    public function test_each_child_can_request_to_join_an_approval_team_with_the_parent_email(): void
    {
        $parent = User::factory()->create();
        $children = Member::factory()->count(2)->create(['user_id' => $parent->id]);
        $team = Team::factory()->create(['join_mode' => TeamJoinModeEnum::APPROVAL]);
        foreach ($children as $child) {
            Livewire::actingAs($parent)->test(JoinTeam::class)
                ->set('memberId', $child->id)->call('selectTeam', $team->id)->assertSet('requestSent', true);
            $this->assertDatabaseHas('team_join_requests', ['team_id' => $team->id,
                'member_id' => $child->id, 'user_id' => $parent->id, 'email' => $parent->email, 'name' => $child->name]);
        }
        Livewire::test(JoinTeam::class)->set('memberId', $children[0]->id)->call('selectTeam', $team->id)
            ->assertSet('requestError', 'Žiadosť o pripojenie už bola odoslaná.');
        $this->assertSame(2, TeamJoinRequest::count());
        $this->assertSame(0, $team->enrolledMembers()->count());
    }

    public function test_each_child_can_request_the_default_team_after_switching_profiles(): void
    {
        $parent = User::factory()->create();
        $children = Member::factory()->count(2)->create(['user_id' => $parent->id]);
        $team = Team::factory()->create(['join_mode' => TeamJoinModeEnum::APPROVAL]);
        Setting::set('default_team_id', $team->id);
        Livewire::actingAs($parent)->test(JoinDefaultTeam::class)
            ->set('memberId', $children[0]->id)->call('submitJoinRequest')->assertSet('requestSent', true)
            ->set('memberId', $children[1]->id)->assertSet('requestSent', false)
            ->assertSeeHtml('wire:click="submitJoinRequest"')->call('submitJoinRequest')->assertSet('requestSent', true)
            ->set('memberId', $children[0]->id)->call('submitJoinRequest')
            ->assertSet('requestError', 'Žiadosť o pripojenie už bola odoslaná.');
        $this->assertSame(2, TeamJoinRequest::where('team_id', $team->id)->where('email', $parent->email)->count());
    }

    public function test_legacy_join_requests_keep_unique_team_emails(): void
    {
        config(['family.enabled' => false]);
        $request = TeamJoinRequest::factory()->create();
        $this->expectException(QueryException::class);
        TeamJoinRequest::factory()->create(['team_id' => $request->team_id, 'email' => $request->email]);
    }

    public function test_join_request_identity_stays_with_the_child_after_a_parent_email_change(): void
    {
        $child = Member::factory()->create();
        $request = TeamJoinRequest::factory()->approved()->create(['member_id' => $child->id, 'user_id' => $child->user_id]);
        $this->expectException(QueryException::class);
        TeamJoinRequest::factory()->create(['team_id' => $request->team_id, 'member_id' => $child->id,
            'user_id' => $child->user_id, 'email' => 'changed@example.test']);
    }

    public function test_switching_children_restores_the_default_team_join_button(): void
    {
        $parent = User::factory()->create();
        $children = Member::factory()->count(2)->create(['user_id' => $parent->id]);
        $team = Team::factory()->create(['join_mode' => TeamJoinModeEnum::OPEN]);
        Setting::set('default_team_id', $team->id);
        Livewire::actingAs($parent)->test(JoinDefaultTeam::class)
            ->call('joinDirectly')->assertHasErrors(['memberId'])
            ->set('memberId', $children[0]->id)->assertHasNoErrors()->call('joinDirectly')->assertSet('joinedDirectly', true)
            ->set('codeSuccess', true)->set('requestError', 'Old error')->set('codeError', 'Old code error')->set('inviteCode', 'OLD')
            ->set('memberId', $children[1]->id)->assertSet('joinedDirectly', false)
            ->assertSet('codeSuccess', false)->assertSet('requestError', null)->assertSet('codeError', null)->assertSet('inviteCode', '')
            ->assertSeeHtml('wire:click="joinDirectly"')->call('joinDirectly')->assertSet('joinedDirectly', true);
        $this->assertSame(2, $team->enrolledMembers()->count());
        $this->assertSame(0, $parent->teams()->count());
    }

    public function test_switching_children_restores_the_team_search_join_button(): void
    {
        $parent = User::factory()->create();
        $children = Member::factory()->count(2)->create(['user_id' => $parent->id]);
        $team = Team::factory()->create(['join_mode' => TeamJoinModeEnum::OPEN, 'name' => ['sk' => 'Family team']]);
        Livewire::actingAs($parent)->test(JoinTeam::class)->set('search', 'Family')
            ->set('memberId', $children[0]->id)->call('selectTeam', $team->id)->assertSet('joinedDirectly', true)
            ->set('requestSent', true)->set('codeSuccess', true)->set('requestError', 'Old error')->set('codeError', 'Old code error')
            ->set('memberId', $children[1]->id)->assertSet('joinedDirectly', false)->assertSet('selectedTeamId', null)
            ->assertSet('requestSent', false)->assertSet('codeSuccess', false)->assertSet('requestError', null)->assertSet('codeError', null)
            ->assertSeeHtml('wire:click="selectTeam')->call('selectTeam', $team->id)->assertSet('joinedDirectly', true);
        $this->assertSame(2, $team->enrolledMembers()->count());
    }

    public function test_a_late_payment_does_not_restore_a_cancelled_place_taken_by_someone_else(): void
    {
        $first = Member::factory()->create();
        $second = Member::factory()->create();
        $training = Training::factory()->create(['pricing_type' => TrainingPricingTypeEnum::PAID, 'price_amount' => 20, 'max_capacity' => 1]);
        $old = RegistrationService::registerTraining($training, $first->user, $first, []);
        $old->update(['status' => RegistrationStatusEnum::Cancelled]);
        $new = RegistrationService::registerTraining($training, $second->user, $second, []);
        $payment = $old->payments()->firstOrFail();
        $payment->update(['status' => PaymentStatusEnum::COMPLETED]);
        app(PaymentService::class)->processPaymentCompleted($payment, false);
        $this->assertSame(RegistrationStatusEnum::Cancelled, $old->fresh()->status);
        $this->assertSame(RegistrationStatusEnum::Pending, $new->fresh()->status);
        $this->assertSame(1, $training->spotHoldingRegistrations()->count());
    }

    public function test_membership_qr_note_does_not_use_parent_names_for_missing_child_names(): void
    {
        $parent = User::factory()->create(['first_name' => 'Parent', 'last_name' => 'Owner']);
        $child = Member::factory()->create(['user_id' => $parent->id, 'first_name' => 'Anna', 'last_name' => null]);
        $season = TeamSeason::factory()->create(['payment_note' => '{{meno}} {{priezvisko}}']);
        $child->enroll($season->team);
        $membership = app(SeasonService::class)->findOrCreateMembership($season, $child);
        $this->assertSame('Anna', $membership->getQrPaymentNote());
    }

    public function test_approved_training_still_asks_for_the_unpaid_childs_membership(): void
    {
        $parent = User::factory()->create();
        $self = $parent->legacyMember();
        $child = Member::factory()->create(['user_id' => $parent->id]);
        $season = TeamSeason::factory()->create();
        $self->enroll($season->team);
        $child->enroll($season->team);
        app(SeasonService::class)->findOrCreateMembership($season, $self)->update(['status' => MembershipStatusEnum::ACTIVE]);
        app(SeasonService::class)->findOrCreateMembership($season, $child);
        $training = Training::factory()->create(['team_id' => $season->team_id, 'team_season_id' => $season->id,
            'pricing_type' => TrainingPricingTypeEnum::MEMBERSHIP_REQUIRED]);
        TrainingRegistration::factory()->create(['training_id' => $training->id, 'member_id' => $child->id,
            'user_id' => $parent->id, 'status' => RegistrationStatusEnum::Approved]);

        Livewire::actingAs($parent)->test('training-registration-form', ['training' => $training, 'memberId' => $child->id])
            ->assertSet('registrationState', 'membership_needed');
    }

    public function test_saved_full_name_does_not_change_when_the_childs_profile_is_edited(): void
    {
        $child = Member::factory()->create(['first_name' => 'Anna', 'last_name' => 'Original']);
        $training = Training::factory()->free()->create(['registration_form_schema' => [
            ['name' => 'full', 'type' => 'full_name'],
        ]]);
        $record = TrainingRegistration::factory()->create(['training_id' => $training->id,
            'member_id' => $child->id, 'user_id' => $child->user_id, 'form_data' => ['full' => 'Anna Original']]);
        $child->update(['last_name' => 'New']);

        $this->assertSame('Anna Original', $record->fresh()->athleteName());
        $this->assertStringStartsWith('Anna Original - ', $record->fresh()->getPaymentDescription());
    }

    public function test_saved_first_name_does_not_pick_up_a_later_profile_last_name(): void
    {
        $child = Member::factory()->create(['first_name' => 'Anna', 'last_name' => 'New']);
        $training = Training::factory()->free()->create(['registration_form_schema' => [
            ['name' => 'first', 'type' => 'first_name'],
        ]]);
        $record = TrainingRegistration::factory()->create(['training_id' => $training->id,
            'member_id' => $child->id, 'user_id' => $child->user_id, 'form_data' => ['first' => 'Anna Original']]);

        $this->assertSame('Anna Original', $record->athleteName());
        $record->update(['form_data' => []]);
        $this->assertSame('Anna New', $record->athleteName());
    }
}
