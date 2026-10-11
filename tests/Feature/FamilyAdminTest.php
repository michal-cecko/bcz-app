<?php

namespace Tests\Feature;

use App\Enums\MembershipStatusEnum;
use App\Enums\PaymentMethodEnum;
use App\Enums\PaymentStatusEnum;
use App\Enums\RoleEnum;
use App\Enums\TrainingPricingTypeEnum;
use App\Filament\Resources\Memberships\Pages\CreateMembership;
use App\Filament\Resources\Memberships\Pages\EditMembership;
use App\Filament\Resources\Memberships\Pages\ListMemberships;
use App\Filament\Resources\Payments\Pages\ListPayments;
use App\Filament\Resources\TeamSeasons\Pages\ViewTeamSeason;
use App\Filament\Resources\TeamSeasons\RelationManagers\MembershipsRelationManager;
use App\Filament\Resources\Trainings\Pages\EditTraining;
use App\Filament\Resources\Trainings\RelationManagers\RegistrationsRelationManager;
use App\Models\Member;
use App\Models\Membership;
use App\Models\Team;
use App\Models\TeamSeason;
use App\Models\Training;
use App\Models\User;
use App\Services\SeasonService;
use Filament\Actions\CreateAction;
use Filament\Actions\EditAction;
use Filament\Actions\Testing\TestAction;
use Filament\Facades\Filament;
use Filament\Forms\Components\Select;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Illuminate\Validation\ValidationException;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class FamilyAdminTest extends TestCase
{
    use RefreshDatabase;

    private Team $team;

    protected function setUp(): void
    {
        parent::setUp();
        config(['family.enabled' => true]);
        Mail::fake();
        Notification::fake();
        foreach (RoleEnum::cases() as $role) {
            Role::firstOrCreate(['name' => $role->value, 'guard_name' => 'web']);
        }
        $this->team = Team::factory()->create();
        $admin = User::factory()->create();
        $admin->assignRole(RoleEnum::SUPER_ADMIN);
        $this->actingAs($admin);
        Filament::setCurrentPanel(Filament::getPanel('admin'));
        Filament::setTenant($this->team);
        Filament::bootCurrentPanel();
    }

    public function test_admin_can_issue_a_membership_for_a_child_with_parent_contact(): void
    {
        $child = Member::factory()->create();
        $child->enroll($this->team);
        $season = TeamSeason::factory()->create(['team_id' => $this->team->id]);
        Livewire::test(CreateMembership::class)->fillForm([
            'member_id' => $child->id, 'team_season_id' => $season->id, 'status' => 'pending',
            'fee_amount' => 30, 'fee_currency' => 'EUR', 'starts_at' => today()->toDateString(), 'ends_at' => $season->ends_at->toDateString(),
        ])->call('create')->assertHasNoFormErrors();
        $this->assertDatabaseHas('memberships', ['member_id' => $child->id, 'user_id' => $child->user_id]);
    }

    public function test_admin_membership_form_rejects_a_child_from_another_team(): void
    {
        $child = Member::factory()->create();
        $child->enroll(Team::factory()->create());
        $season = TeamSeason::factory()->create(['team_id' => $this->team->id]);
        Livewire::test(CreateMembership::class)->fillForm([
            'member_id' => $child->id, 'team_season_id' => $season->id, 'status' => 'pending',
            'fee_amount' => 30, 'fee_currency' => 'EUR', 'starts_at' => today()->toDateString(), 'ends_at' => $season->ends_at->toDateString(),
        ])->call('create')->assertHasFormErrors(['member_id']);
        $this->assertSame(0, $child->memberships()->count());
    }

    public function test_mark_free_rejects_a_historical_membership_with_a_current_renewal(): void
    {
        $child = Member::factory()->create();
        $child->enroll($this->team);
        $season = TeamSeason::factory()->create(['team_id' => $this->team->id]);
        $old = app(SeasonService::class)->findOrCreateMembership($season, $child);
        $old->update(['status' => MembershipStatusEnum::CANCELLED]);
        $current = app(SeasonService::class)->renewMembership($old);
        Livewire::test(MembershipsRelationManager::class, ['ownerRecord' => $season, 'pageClass' => ViewTeamSeason::class])
            ->callAction(TestAction::make('markFree')->table($old))->assertNotified(__('family.membership_exists'));
        $this->assertSame(MembershipStatusEnum::CANCELLED, $old->fresh()->status);
        $this->assertFalse($old->fresh()->is_free);
        $this->assertSame(MembershipStatusEnum::PENDING, $current->fresh()->status);
    }

    public function test_admin_membership_edit_rejects_a_conflicting_activation(): void
    {
        $child = Member::factory()->create();
        $child->enroll($this->team);
        $season = TeamSeason::factory()->create(['team_id' => $this->team->id]);
        $old = app(SeasonService::class)->findOrCreateMembership($season, $child);
        $old->update(['status' => MembershipStatusEnum::CANCELLED]);
        $current = app(SeasonService::class)->renewMembership($old);
        Livewire::test(EditMembership::class, ['record' => $old->id])
            ->fillForm(['status' => 'active'])->call('save')->assertHasFormErrors(['status']);
        Livewire::test(ListMemberships::class)->callAction(TestAction::make(EditAction::class)->table($old), ['status' => 'pending'])
            ->assertHasActionErrors(['status']);
        $this->assertSame(MembershipStatusEnum::CANCELLED, $old->fresh()->status);
        $this->assertSame(MembershipStatusEnum::PENDING, $current->fresh()->status);
    }

    public function test_admin_membership_creation_rejects_a_second_current_fee(): void
    {
        $child = Member::factory()->create();
        $child->enroll($this->team);
        $season = TeamSeason::factory()->create(['team_id' => $this->team->id]);
        $current = app(SeasonService::class)->findOrCreateMembership($season, $child);
        Livewire::test(CreateMembership::class)->fillForm([
            'member_id' => $child->id, 'team_season_id' => $season->id, 'status' => 'pending',
            'fee_amount' => 30, 'fee_currency' => 'EUR', 'starts_at' => today()->toDateString(), 'ends_at' => $season->ends_at->toDateString(),
        ])->call('create')->assertHasFormErrors(['status']);
        $this->assertSame($current->id, $child->memberships()->sole()->id);
    }

    public function test_admin_can_mark_the_current_membership_free_and_edit_it(): void
    {
        $child = Member::factory()->create();
        $child->enroll($this->team);
        $season = TeamSeason::factory()->create(['team_id' => $this->team->id]);
        $current = app(SeasonService::class)->findOrCreateMembership($season, $child);
        Livewire::test(MembershipsRelationManager::class, ['ownerRecord' => $season, 'pageClass' => ViewTeamSeason::class])
            ->callAction(TestAction::make('markFree')->table($current))->assertHasNoActionErrors();
        $this->assertSame(MembershipStatusEnum::ACTIVE, $current->fresh()->status);
        $this->assertTrue($current->fresh()->is_free);
        $this->assertEquals(0, $current->fresh()->fee_amount);
        Livewire::test(EditMembership::class, ['record' => $current->id])
            ->fillForm(['ends_at' => $season->ends_at->addDay()->toDateString()])->call('save')->assertHasNoFormErrors();
    }

    public function test_mark_free_rejects_a_conflict_without_a_season_and_keeps_other_teams_separate(): void
    {
        $child = Member::factory()->create();
        $old = Membership::factory()->cancelled()->create(['member_id' => $child->id, 'team_id' => $this->team->id]);
        $current = app(SeasonService::class)->renewMembership($old);
        $otherTeam = Team::factory()->create();
        Filament::setTenant($otherTeam);
        $other = Membership::factory()->pending()->create(['member_id' => $child->id, 'team_id' => $otherTeam->id]);
        Filament::setTenant($this->team);
        app(SeasonService::class)->markMembershipFree($other);
        $this->assertSame(MembershipStatusEnum::ACTIVE, $other->fresh()->status);
        try {
            app(SeasonService::class)->markMembershipFree($old);
            $this->fail('A current membership must prevent activating the historical row.');
        } catch (ValidationException $exception) {
            $this->assertArrayHasKey('status', $exception->errors());
        }
        $this->assertSame(MembershipStatusEnum::CANCELLED, $old->fresh()->status);
        $this->assertSame(MembershipStatusEnum::PENDING, $current->fresh()->status);
    }

    public function test_admin_registration_uses_the_same_member_booking_operation(): void
    {
        $child = Member::factory()->create();
        $child->enroll($this->team);
        $training = Training::factory()->free()->create(['team_id' => $this->team->id, 'registration_form_schema' => []]);
        Livewire::test(RegistrationsRelationManager::class, ['ownerRecord' => $training, 'pageClass' => EditTraining::class])
            ->callAction(TestAction::make(CreateAction::class)->table(), ['member_id' => $child->id, 'status' => 'approved', 'send_notification' => false])
            ->assertHasNoActionErrors();
        $this->assertDatabaseHas('training_registrations', ['member_id' => $child->id, 'user_id' => $child->user_id, 'training_id' => $training->id]);
    }

    public function test_admin_paid_registration_with_notification_has_one_pending_payment(): void
    {
        $child = Member::factory()->create();
        $child->enroll($this->team);
        $training = Training::factory()->create(['team_id' => $this->team->id, 'registration_form_schema' => [],
            'pricing_type' => TrainingPricingTypeEnum::PAID, 'price_amount' => 20]);
        Livewire::test(RegistrationsRelationManager::class, ['ownerRecord' => $training, 'pageClass' => EditTraining::class])
            ->callAction(TestAction::make(CreateAction::class)->table(), ['member_id' => $child->id,
                'status' => 'pending', 'send_notification' => true])->assertHasNoActionErrors();

        $record = $child->trainingRegistrations()->sole();
        $this->assertSame(1, $record->payments()->where('status', PaymentStatusEnum::PENDING)->count());
        $this->assertEquals(20, $record->payments()->sum('amount'));
    }

    public function test_admin_can_record_a_childs_payment_from_the_payment_list(): void
    {
        $child = Member::factory()->create();
        $child->user->teams()->detach($this->team);
        $child->enroll($this->team);
        $season = TeamSeason::factory()->create(['team_id' => $this->team->id, 'fee_amount' => 100, 'prorate_fee' => false]);
        $membership = app(SeasonService::class)->findOrCreateMembership($season, $child);
        $other = Member::factory()->create();
        $other->user->teams()->detach($this->team);
        $other->enroll(Team::factory()->create());
        $staff = User::factory()->create();
        $staff->teams()->attach($this->team, ['role' => RoleEnum::COACH->value]);

        Livewire::test(ListPayments::class)->mountAction('recordPayment')
            ->assertFormFieldExists('user_id', function (Select $field) use ($child, $other, $staff): bool {
                $options = $field->getOptions();
                $this->assertArrayHasKey($child->user_id, $options);
                $this->assertArrayHasKey($staff->id, $options);
                $this->assertArrayNotHasKey($other->user_id, $options);

                return true;
            })
            ->fillForm(['user_id' => $child->user_id, 'payable_type' => 'membership',
                'payable_id' => $membership->id, 'amount' => 100, 'currency' => 'EUR',
                'payment_method' => PaymentMethodEnum::CASH->value, 'notify_customer' => false])
            ->callMountedAction()
            ->assertHasNoActionErrors();

        $this->assertSame(MembershipStatusEnum::ACTIVE, $membership->fresh()->status);
        $this->assertDatabaseHas('payments', ['user_id' => $child->user_id, 'team_id' => $this->team->id,
            'payable_id' => $membership->id, 'status' => PaymentStatusEnum::COMPLETED->value]);
    }
}
