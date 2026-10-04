<?php

namespace Tests\Feature\Filament;

use App\Enums\MembershipStatusEnum;
use App\Enums\PaymentMethodEnum;
use App\Enums\PayoutStatusEnum;
use App\Enums\RoleEnum;
use App\Filament\Resources\Teams\Pages\ViewTeam;
use App\Filament\Resources\Teams\RelationManagers\InvitationsRelationManager;
use App\Filament\Resources\Teams\RelationManagers\MembersRelationManager;
use App\Filament\Resources\Teams\RelationManagers\PaymentMethodsRelationManager;
use App\Filament\Resources\Teams\RelationManagers\PayoutsRelationManager;
use App\Models\Membership;
use App\Models\PaymentMethod;
use App\Models\Team;
use App\Models\TeamInvitation;
use App\Models\TeamPayout;
use App\Models\TeamSeason;
use App\Models\User;
use Database\Seeders\ShieldPermissionSeeder;
use Filament\Actions\Testing\TestAction;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

/**
 * Same discrepancy as {@see SeasonsRelationManagerActionsTest} (issue #32), but
 * for the other Team relation managers that also default to Filament's
 * read-only-on-`ViewRecord` behaviour: Invitations, Payouts, Members, and
 * PaymentMethods.
 */
class TeamRelationManagersActionsTest extends TestCase
{
    use RefreshDatabase;

    protected Team $team;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        foreach (RoleEnum::cases() as $role) {
            Role::firstOrCreate(['name' => $role->value, 'guard_name' => 'web']);
        }

        $this->seed(ShieldPermissionSeeder::class);

        $this->team = Team::factory()->create();

        $this->admin = User::factory()->create();
        $this->admin->assignRole(RoleEnum::SUPER_ADMIN);
    }

    protected function actingAsTenantUser(User $user): void
    {
        $this->actingAs($user);
        Filament::setCurrentPanel(Filament::getPanel('admin'));
        Filament::setTenant($this->team);
        Filament::bootCurrentPanel();
    }

    public function test_invitations_delete_action_is_visible_on_the_view_page_for_a_team_manager(): void
    {
        $invitation = TeamInvitation::factory()->create(['team_id' => $this->team->id]);

        $this->actingAsTenantUser($this->admin->fresh());

        Livewire::test(InvitationsRelationManager::class, [
            'ownerRecord' => $this->team,
            'pageClass' => ViewTeam::class,
        ])->assertTableActionVisible('delete', $invitation);
    }

    public function test_invitations_delete_action_is_hidden_on_the_view_page_for_an_unrelated_user(): void
    {
        $invitation = TeamInvitation::factory()->create(['team_id' => $this->team->id]);

        $athlete = User::factory()->create();
        $athlete->teams()->attach($this->team->id, ['role' => RoleEnum::ATHLETE->value, 'is_active' => true, 'joined_at' => now()]);

        $this->actingAsTenantUser($athlete->fresh());

        Livewire::test(InvitationsRelationManager::class, [
            'ownerRecord' => $this->team,
            'pageClass' => ViewTeam::class,
        ])->assertTableActionHidden('delete', $invitation);
    }

    public function test_payouts_create_and_delete_actions_are_visible_on_the_view_page_for_a_team_manager(): void
    {
        $payout = TeamPayout::factory()->create(['team_id' => $this->team->id]);

        $this->actingAsTenantUser($this->admin->fresh());

        Livewire::test(PayoutsRelationManager::class, [
            'ownerRecord' => $this->team,
            'pageClass' => ViewTeam::class,
        ])
            ->assertTableActionVisible('create')
            ->assertTableActionVisible('delete', $payout);
    }

    public function test_only_a_platform_admin_can_mark_a_payout_as_paid(): void
    {
        $payout = TeamPayout::factory()->create(['team_id' => $this->team->id, 'status' => PayoutStatusEnum::PENDING]);

        $teamAdmin = User::factory()->create();
        $teamAdmin->teams()->attach($this->team->id, ['role' => RoleEnum::TEAM_ADMIN->value, 'is_active' => true, 'joined_at' => now()]);

        $this->actingAsTenantUser($teamAdmin->fresh());

        Livewire::test(PayoutsRelationManager::class, [
            'ownerRecord' => $this->team,
            'pageClass' => ViewTeam::class,
        ])->assertTableActionHidden('markPaid', $payout);

        $this->actingAsTenantUser($this->admin->fresh());

        Livewire::test(PayoutsRelationManager::class, [
            'ownerRecord' => $this->team,
            'pageClass' => ViewTeam::class,
        ])->assertTableActionVisible('markPaid', $payout);
    }

    public function test_payouts_create_and_delete_actions_are_hidden_on_the_view_page_for_an_unrelated_user(): void
    {
        $payout = TeamPayout::factory()->create(['team_id' => $this->team->id]);

        $athlete = User::factory()->create();
        $athlete->teams()->attach($this->team->id, ['role' => RoleEnum::ATHLETE->value, 'is_active' => true, 'joined_at' => now()]);

        $this->actingAsTenantUser($athlete->fresh());

        Livewire::test(PayoutsRelationManager::class, [
            'ownerRecord' => $this->team,
            'pageClass' => ViewTeam::class,
        ])
            ->assertTableActionHidden('create')
            ->assertTableActionHidden('delete', $payout);
    }

    public function test_members_detach_action_is_visible_on_the_view_page_for_a_team_manager(): void
    {
        $member = User::factory()->create();
        $member->teams()->attach($this->team->id, ['role' => RoleEnum::ATHLETE->value, 'is_active' => true, 'joined_at' => now()]);

        $this->actingAsTenantUser($this->admin->fresh());

        Livewire::test(MembersRelationManager::class, [
            'ownerRecord' => $this->team,
            'pageClass' => ViewTeam::class,
        ])->assertTableActionVisible('detach', $member);
    }

    public function test_add_membership_saves_the_fee_the_admin_entered(): void
    {
        $season = TeamSeason::factory()->create([
            'team_id' => $this->team->id,
            'fee_amount' => 120.00,
            'fee_currency' => 'EUR',
        ]);

        $member = User::factory()->create();
        $member->assignRole(RoleEnum::CUSTOMER);
        $member->teams()->attach($this->team->id, ['role' => RoleEnum::ATHLETE->value, 'is_active' => true, 'joined_at' => now()]);

        $this->actingAsTenantUser($this->admin->fresh());

        Livewire::test(MembersRelationManager::class, [
            'ownerRecord' => $this->team,
            'pageClass' => ViewTeam::class,
        ])
            ->callAction(TestAction::make('addMembership')->table($member), [
                'team_season_id' => $season->id,
                'is_free' => false,
                'fee_amount' => '60',
                'fee_currency' => 'CZK',
            ])
            ->assertHasNoActionErrors();

        $membership = Membership::where('user_id', $member->id)->sole();
        $this->assertEquals(60.00, (float) $membership->fee_amount);
        $this->assertSame('CZK', $membership->fee_currency);
    }

    public function test_add_membership_with_a_zero_fee_is_free_and_active(): void
    {
        $season = TeamSeason::factory()->create(['team_id' => $this->team->id, 'fee_amount' => 120.00]);

        $member = User::factory()->create();
        $member->assignRole(RoleEnum::CUSTOMER);
        $member->teams()->attach($this->team->id, ['role' => RoleEnum::ATHLETE->value, 'is_active' => true, 'joined_at' => now()]);

        $this->actingAsTenantUser($this->admin->fresh());

        Livewire::test(MembersRelationManager::class, [
            'ownerRecord' => $this->team,
            'pageClass' => ViewTeam::class,
        ])
            ->callAction(TestAction::make('addMembership')->table($member), [
                'team_season_id' => $season->id,
                'is_free' => false,
                'fee_amount' => '0',
                'fee_currency' => 'EUR',
            ])
            ->assertHasNoActionErrors();

        $membership = Membership::where('user_id', $member->id)->sole();
        $this->assertTrue($membership->is_free);
        $this->assertSame(MembershipStatusEnum::ACTIVE, $membership->status);
    }

    public function test_members_detach_action_is_hidden_on_the_view_page_for_an_unrelated_user(): void
    {
        $member = User::factory()->create();
        $member->teams()->attach($this->team->id, ['role' => RoleEnum::ATHLETE->value, 'is_active' => true, 'joined_at' => now()]);

        $athlete = User::factory()->create();
        $athlete->teams()->attach($this->team->id, ['role' => RoleEnum::ATHLETE->value, 'is_active' => true, 'joined_at' => now()]);

        $this->actingAsTenantUser($athlete->fresh());

        Livewire::test(MembersRelationManager::class, [
            'ownerRecord' => $this->team,
            'pageClass' => ViewTeam::class,
        ])->assertTableActionHidden('detach', $member);
    }

    public function test_payment_methods_attach_and_detach_actions_are_visible_on_the_view_page_for_a_team_manager(): void
    {
        $method = PaymentMethod::create([
            'method' => PaymentMethodEnum::GOPAY->value,
            'title' => 'GoPay',
            'is_active' => true,
        ]);
        $this->team->paymentMethods()->attach($method->id, ['is_enabled' => true, 'sort_order' => 0]);

        $this->actingAsTenantUser($this->admin->fresh());

        Livewire::test(PaymentMethodsRelationManager::class, [
            'ownerRecord' => $this->team,
            'pageClass' => ViewTeam::class,
        ])
            ->assertTableActionVisible('attach')
            ->assertTableActionVisible('detach', $method);
    }

    public function test_payment_methods_attach_and_detach_actions_are_hidden_on_the_view_page_for_an_unrelated_user(): void
    {
        $method = PaymentMethod::create([
            'method' => PaymentMethodEnum::GOPAY->value,
            'title' => 'GoPay',
            'is_active' => true,
        ]);
        $this->team->paymentMethods()->attach($method->id, ['is_enabled' => true, 'sort_order' => 0]);

        $athlete = User::factory()->create();
        $athlete->teams()->attach($this->team->id, ['role' => RoleEnum::ATHLETE->value, 'is_active' => true, 'joined_at' => now()]);

        $this->actingAsTenantUser($athlete->fresh());

        Livewire::test(PaymentMethodsRelationManager::class, [
            'ownerRecord' => $this->team,
            'pageClass' => ViewTeam::class,
        ])
            ->assertTableActionHidden('attach')
            ->assertTableActionHidden('detach', $method);
    }
}
