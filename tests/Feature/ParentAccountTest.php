<?php

namespace Tests\Feature;

use App\Enums\DraftStatusEnum;
use App\Enums\GenderEnum;
use App\Enums\MembershipStatusEnum;
use App\Enums\RoleEnum;
use App\Filament\Pages\Auth\UserSetupWizard;
use App\Filament\Pages\MyFamily;
use App\Filament\Resources\Users\Pages\CreateUser;
use App\Filament\Resources\Users\Pages\EditUser;
use App\Filament\Resources\Users\RelationManagers\MembersRelationManager;
use App\Models\Member;
use App\Models\Membership;
use App\Models\Payment;
use App\Models\Team;
use App\Models\TeamJoinRequest;
use App\Models\Training;
use App\Models\TrainingRegistration;
use App\Models\TrainingWaitlist;
use App\Models\User;
use App\Services\MemberProfileService;
use App\Services\SeasonService;
use App\Services\TrainingFilterService;
use Filament\Actions\Testing\TestAction;
use Filament\Facades\Filament;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\DataProvider;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ParentAccountTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        config(['family.enabled' => true]);
        Notification::fake();
        foreach (RoleEnum::cases() as $role) {
            Role::firstOrCreate(['name' => $role->value, 'guard_name' => 'web']);
        }
        Filament::setCurrentPanel(Filament::getPanel('customer'));
    }

    private function customer(bool $isParent = false): User
    {
        $user = User::factory()->create(['is_parent' => $isParent, 'password_set_at' => now()]);
        $user->assignRole(RoleEnum::CUSTOMER);

        return $user;
    }

    private function admin(): User
    {
        $user = User::factory()->create();
        $user->assignRole(RoleEnum::SUPER_ADMIN);
        $this->actingAs($user);

        return $user;
    }

    #[DataProvider('familyEditors')]
    public function test_family_tab_creates_a_profile_for_its_owner(bool $asAdmin): void
    {
        $parent = $this->customer(true);
        $other = $this->customer(true);
        $actor = $asAdmin ? $this->admin() : $parent;

        Livewire::actingAs($actor)->test(MembersRelationManager::class, ['ownerRecord' => $parent, 'pageClass' => EditUser::class])
            ->callAction(TestAction::make('addMember')->table(), [
                'first_name' => 'New', 'last_name' => 'Child', 'birth_date' => '2015-01-01',
                'user_id' => $other->id, 'has_free_membership' => true,
            ])->assertHasNoActionErrors();

        $this->assertSame('New Child', $parent->members()->sole()->name);
        $this->assertFalse($parent->members()->sole()->has_free_membership);
        $this->assertSame(0, $other->members()->count());
        $this->assertSame(0, Membership::count());
    }

    public static function familyEditors(): array
    {
        return ['parent' => [false], 'admin' => [true]];
    }

    #[DataProvider('familyDeleteScreens')]
    public function test_unused_family_profile_is_deleted_only_after_confirmation(bool $asAdmin, bool $onFamilyPage): void
    {
        $parent = $this->customer(true);
        $members = Member::factory()->count(2)->create(['user_id' => $parent->id]);
        $actor = $asAdmin ? $this->admin() : $parent;
        $page = Livewire::actingAs($actor)->test(
            $onFamilyPage ? MyFamily::class : MembersRelationManager::class,
            $onFamilyPage ? [] : ['ownerRecord' => $parent, 'pageClass' => EditUser::class],
        );

        $page->mountAction(TestAction::make('delete')->table($members[0]))
            ->assertActionMounted(TestAction::make('delete')->table($members[0]));
        $this->assertDatabaseHas('members', ['id' => $members[0]->id]);
        $page->callMountedAction()->assertHasNoActionErrors()->assertNotified();

        $this->assertDatabaseMissing('members', ['id' => $members[0]->id]);
        $this->assertDatabaseHas('members', ['id' => $members[1]->id]);
        $this->assertTrue($parent->fresh()->is_parent);
    }

    public static function familyDeleteScreens(): array
    {
        return ['parent family page' => [false, true], 'parent family tab' => [false, false], 'admin family tab' => [true, false]];
    }

    #[DataProvider('profileHistory')]
    public function test_family_profile_with_history_cannot_be_deleted(string $history): void
    {
        $parent = $this->customer(true);
        $member = Member::factory()->create(['user_id' => $parent->id]);
        $this->actingAs($parent);
        $team = Team::factory()->create();
        $reference = match ($history) {
            'team' => $member->enroll($team),
            'membership' => Membership::factory()->cancelled()->create(['member_id' => $member->id, 'user_id' => $parent->id]),
            'training' => TrainingRegistration::factory()->cancelled()->create(['member_id' => $member->id, 'user_id' => $parent->id]),
            'waitlist' => TrainingWaitlist::create(['training_id' => Training::factory()->create()->id, 'member_id' => $member->id, 'user_id' => $parent->id]),
            'join request' => TeamJoinRequest::factory()->rejected()->create(['member_id' => $member->id, 'user_id' => $parent->id]),
        };
        $payment = $reference instanceof Membership ? Payment::factory()->create([
            'payable_id' => $reference->id, 'team_id' => $reference->team_id, 'user_id' => $parent->id,
        ]) : null;
        $payment?->refresh();

        Livewire::test(MyFamily::class)->callAction(TestAction::make('delete')->table($member))
            ->assertNotified(__('family.delete_blocked'));

        $this->assertDatabaseHas('members', ['id' => $member->id]);
        if ($reference) {
            $this->assertDatabaseHas($reference->getTable(), ['id' => $reference->id, 'member_id' => $member->id]);
        } else {
            $this->assertDatabaseHas('member_team', ['team_id' => $team->id, 'member_id' => $member->id]);
        }
        if ($payment) {
            $this->assertSame($payment->getAttributes(), $payment->fresh()->getAttributes());
        }
    }

    public static function profileHistory(): array
    {
        return ['team' => ['team'], 'membership' => ['membership'], 'training' => ['training'],
            'waitlist' => ['waitlist'], 'join request' => ['join request']];
    }

    public function test_history_hidden_by_a_global_scope_still_blocks_deletion(): void
    {
        $parent = $this->customer(true);
        $member = Member::factory()->create(['user_id' => $parent->id]);
        Membership::factory()->cancelled()->create(['member_id' => $member->id, 'user_id' => $parent->id]);
        $scopes = Membership::getAllGlobalScopes();
        try {
            Membership::addGlobalScope('hidden-history', fn ($query) => $query->whereRaw('1 = 0'));
            $this->assertFalse($member->memberships()->exists());
            $this->assertFalse(app(MemberProfileService::class)->deleteMember($member));
        } finally {
            Membership::setAllGlobalScopes($scopes);
        }
        $this->assertDatabaseHas('members', ['id' => $member->id]);
    }

    public function test_parent_cannot_create_or_delete_profiles_for_another_family(): void
    {
        $parent = $this->customer(true);
        $other = $this->customer(true);
        $foreign = Member::factory()->create(['user_id' => $other->id]);
        $this->actingAs($parent);
        $this->assertFalse(Gate::allows('create', [Member::class, $other]));
        $this->assertFalse(Gate::allows('delete', $foreign));
        Livewire::test(MyFamily::class)->mountAction(TestAction::make('delete')->table($foreign))->assertActionNotMounted();
        $this->assertDatabaseHas('members', ['id' => $foreign->id]);
    }

    public function test_normal_accounts_and_disabled_family_mode_cannot_use_family_actions(): void
    {
        $user = $this->customer();
        $member = Member::factory()->create(['user_id' => $user->id]);
        $this->actingAs($user);
        $this->assertFalse(Gate::allows('create', Member::class));
        $this->assertFalse(Gate::allows('delete', $member));
        $user->update(['is_parent' => true]);
        $member->unsetRelation('user');
        config(['family.enabled' => false]);
        $this->assertFalse(Gate::allows('create', Member::class));
        $this->assertFalse(Gate::allows('delete', $member));
    }

    public function test_normal_account_cannot_create_a_second_profile(): void
    {
        $user = $this->customer();
        app(MemberProfileService::class)->resolveMember($user);
        try {
            app(MemberProfileService::class)->createMember($user, ['first_name' => 'Another person']);
            $this->fail('A second profile was created.');
        } catch (ValidationException $exception) {
            $this->assertArrayHasKey('is_parent', $exception->errors());
        }
        $this->assertSame(1, $user->members()->count());
    }

    public function test_parent_enrolled_only_through_a_child_can_edit_their_own_account(): void
    {
        $user = $this->customer(true);
        $other = $this->customer();
        $team = Team::factory()->create();
        Member::factory()->create(['user_id' => $user->id])->enroll($team);
        $this->assertSame(0, $user->teams()->count());
        $this->actingAs($user);

        $this->get(EditUser::getUrl(panel: 'admin', tenant: $team, parameters: ['record' => $user->id]))->assertOk();
        $this->get(EditUser::getUrl(panel: 'admin', tenant: $team, parameters: ['record' => $other->id]))->assertNotFound();
    }

    public function test_parent_cannot_grant_a_child_a_fee_exemption_through_the_account_editor(): void
    {
        $user = $this->customer(true);
        $member = Member::factory()->create(['user_id' => $user->id]);
        Livewire::actingAs($user)->test(MembersRelationManager::class, ['ownerRecord' => $user, 'pageClass' => EditUser::class])
            ->callAction(TestAction::make('editMember')->table($member), [
                'first_name' => $member->first_name, 'last_name' => $member->last_name,
                'birth_date' => $member->birth_date->toDateString(), 'has_free_membership' => true,
            ])->assertHasNoActionErrors();

        $this->assertFalse($member->fresh()->has_free_membership);
    }

    public function test_parent_mode_cannot_be_disabled_with_several_profiles(): void
    {
        $user = $this->customer(true);
        Member::factory()->count(2)->create(['user_id' => $user->id]);
        Livewire::actingAs($user)->test(EditUser::class, ['record' => $user->id])
            ->fillForm(['is_parent' => false])->call('save')->assertHasFormErrors(['is_parent']);
        $this->assertTrue($user->fresh()->is_parent);
        $this->assertSame(2, $user->members()->count());
    }

    public function test_single_profile_edits_change_training_details_and_keep_account_identity(): void
    {
        $user = $this->customer();
        $member = Member::factory()->create(['user_id' => $user->id, 'birth_date' => '2015-01-01']);
        $accountName = $user->name;
        $accountBirthDate = $user->birth_date->toDateString();
        Livewire::actingAs($user)->test(EditUser::class, ['record' => $user->id])
            ->fillForm(['participant' => ['first_name' => 'Adult', 'last_name' => 'Participant',
                'birth_date' => '2000-01-01', 'gender' => GenderEnum::FEMALE->value]])
            ->call('save')->assertHasNoFormErrors();

        $this->assertSame('Adult Participant', $member->fresh()->name);
        $this->assertSame('2000-01-01', $member->fresh()->birth_date->toDateString());
        $this->assertSame(GenderEnum::FEMALE, $member->fresh()->gender);
        $this->assertTrue(app(TrainingFilterService::class)->matchesAge(new Training(['min_age' => 18]), $member->fresh()));
        $this->assertSame($accountName, $user->fresh()->name);
        $this->assertSame($accountBirthDate, $user->fresh()->birth_date->toDateString());
    }

    public function test_wizard_edits_the_member_and_redirects_a_normal_customer_home(): void
    {
        $user = $this->customer();
        $member = app(MemberProfileService::class)->resolveMember($user);
        Livewire::actingAs($user)->test(UserSetupWizard::class)
            ->set('data.participant.birth_date', '2000-01-01')
            ->set('data.participant.gender', GenderEnum::FEMALE->value)
            ->call('save')->assertHasNoFormErrors()->assertRedirect('/customer');
        $this->assertSame('2000-01-01', $member->fresh()->birth_date->toDateString());
        $this->assertSame(GenderEnum::FEMALE, $member->fresh()->gender);
    }

    public function test_admin_edits_the_member_exemption_used_by_next_season_billing(): void
    {
        $user = $this->customer();
        $member = app(MemberProfileService::class)->resolveMember($user);
        $team = Team::factory()->create();
        $member->enroll($team);
        $this->admin();
        Livewire::test(EditUser::class, ['record' => $user->id])
            ->fillForm(['participant.has_free_membership' => true])->call('save')->assertHasNoFormErrors();
        $this->assertTrue($member->fresh()->has_free_membership);
        $this->assertFalse($user->fresh()->has_free_membership);
        $season = app(SeasonService::class)->createSeasonWithMemberships($team, [
            'name' => 'Next season', 'starts_at' => today(), 'ends_at' => today()->addMonths(3),
            'fee_amount' => 100, 'fee_currency' => 'EUR', 'prorate_fee' => false, 'payment_deadline_days' => 14,
        ]);
        $this->assertEquals(0, $season->memberships()->sole()->fee_amount);
        $this->assertSame(MembershipStatusEnum::ACTIVE, $season->memberships()->sole()->status);
    }

    public function test_parent_member_exemption_does_not_change_a_sibling_or_issued_fees(): void
    {
        $user = $this->customer(true);
        $members = Member::factory()->count(2)->create(['user_id' => $user->id]);
        $team = Team::factory()->create();
        foreach ($members as $member) {
            $member->enroll($team);
        }
        $old = app(SeasonService::class)->createSeasonWithMemberships($team, [
            'name' => 'Issued season', 'starts_at' => today(), 'ends_at' => today()->addMonths(3),
            'fee_amount' => 100, 'fee_currency' => 'EUR', 'prorate_fee' => false, 'payment_deadline_days' => 14,
        ]);
        $this->admin();
        Livewire::test(MembersRelationManager::class, ['ownerRecord' => $user, 'pageClass' => EditUser::class])
            ->callAction(TestAction::make('editMember')->table($members[0]), [
                'first_name' => $members[0]->first_name, 'last_name' => $members[0]->last_name,
                'birth_date' => $members[0]->birth_date->toDateString(), 'has_free_membership' => true,
            ])->assertHasNoActionErrors();
        $this->assertTrue($members[0]->fresh()->has_free_membership);
        $this->assertFalse($members[1]->fresh()->has_free_membership);
        $this->assertEquals([100, 100], $old->memberships()->pluck('fee_amount')->all());
    }

    public function test_customer_cannot_grant_their_member_a_fee_exemption(): void
    {
        $user = $this->customer();
        $member = app(MemberProfileService::class)->resolveMember($user);
        Livewire::actingAs($user)->test(EditUser::class, ['record' => $user->id])
            ->fillForm(['participant.has_free_membership' => true, 'has_free_membership' => true])
            ->call('save')->assertHasNoFormErrors();
        $this->assertFalse($member->fresh()->has_free_membership);
        $this->assertFalse($user->fresh()->has_free_membership);
    }

    public function test_family_flag_keeps_public_athlete_onboarding_for_a_normal_athlete(): void
    {
        $user = $this->customer();
        $user->teams()->attach(Team::factory()->create(), ['role' => RoleEnum::ATHLETE->value]);
        Livewire::actingAs($user)->test(UserSetupWizard::class)
            ->set('data.has_public_profile_athlete', true)
            ->set('data.date_started_working_out', '2020-01-01')
            ->call('save')->assertHasNoFormErrors();
        $this->assertSame(DraftStatusEnum::Pending, $user->fresh()->athleteProfile->draft_status);
    }

    public function test_migration_marks_only_accounts_with_multiple_profiles_as_parents(): void
    {
        $single = $this->customer();
        $family = $this->customer();
        Member::factory()->create(['user_id' => $single->id]);
        $profiles = Member::factory()->count(2)->create(['user_id' => $family->id]);
        Schema::table('users', fn (Blueprint $table) => $table->dropColumn('is_parent'));
        $migration = require database_path('migrations/2026_10_11_125900_add_is_parent_to_users_table.php');
        $migration->up();
        $this->assertFalse($single->fresh()->is_parent);
        $this->assertTrue($family->fresh()->is_parent);
        $this->assertEquals($profiles->pluck('id')->sort()->values()->all(), $family->members()->pluck('id')->sort()->values()->all());
    }

    public function test_admin_creation_stores_participant_details_and_exemption_on_the_member(): void
    {
        $this->admin();
        Livewire::test(CreateUser::class)->fillForm([
            'first_name' => 'Contact', 'last_name' => 'Owner', 'email' => 'created@example.test',
            'roles' => [Role::findByName(RoleEnum::CUSTOMER->value)->id], 'send_welcome_notification' => false,
            'participant' => ['first_name' => 'Actual', 'last_name' => 'Athlete', 'birth_date' => '2000-01-01',
                'has_free_membership' => true],
        ])->call('create')->assertHasNoFormErrors();
        $user = User::where('email', 'created@example.test')->sole();
        $this->assertSame('Contact Owner', $user->name);
        $this->assertSame('Actual Athlete', $user->members()->sole()->name);
        $this->assertTrue($user->members()->sole()->has_free_membership);
        $this->assertFalse($user->has_free_membership);
    }
}
