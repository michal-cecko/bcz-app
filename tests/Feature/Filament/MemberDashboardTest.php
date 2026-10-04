<?php

namespace Tests\Feature\Filament;

use App\Enums\MembershipStatusEnum;
use App\Enums\PaymentMethodEnum;
use App\Enums\RoleEnum;
use App\Filament\Pages\Dashboard;
use App\Filament\Pages\MemberEvents;
use App\Filament\Pages\MemberMembership;
use App\Filament\Pages\MemberPayments;
use App\Filament\Pages\MyTrainings;
use App\Filament\Widgets\MembershipStatusWidget;
use App\Filament\Widgets\RecentPaymentsWidget;
use App\Filament\Widgets\UpcomingTrainingsWidget;
use App\Models\Membership;
use App\Models\PaymentMethod;
use App\Models\Team;
use App\Models\TeamSeason;
use App\Models\User;
use Filament\Facades\Filament;
use Filament\Widgets\AccountWidget;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class MemberDashboardTest extends TestCase
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

        Filament::setCurrentPanel(Filament::getPanel('admin'));
        Filament::bootCurrentPanel();
    }

    protected function actingAsMember(): User
    {
        $user = User::factory()->create();
        $user->assignRole(RoleEnum::CUSTOMER);
        $user->teams()->attach($this->team, ['role' => RoleEnum::ATHLETE->value]);
        $this->actingAs($user);
        Filament::setTenant($this->team);

        return $user;
    }

    protected function actingAsAdmin(): User
    {
        $user = User::factory()->create();
        $user->assignRole(RoleEnum::SUPER_ADMIN);
        $user->teams()->attach($this->team);
        $this->actingAs($user);
        Filament::setTenant($this->team);

        return $user;
    }

    public function test_member_sees_member_widgets(): void
    {
        $this->actingAsMember();

        $dashboard = new Dashboard;
        $widgets = $dashboard->getWidgets();

        $this->assertContains(MembershipStatusWidget::class, $widgets);
        $this->assertContains(UpcomingTrainingsWidget::class, $widgets);
        $this->assertContains(RecentPaymentsWidget::class, $widgets);
    }

    public function test_admin_sees_admin_widgets(): void
    {
        $this->actingAsAdmin();

        $dashboard = new Dashboard;
        $widgets = $dashboard->getWidgets();

        $this->assertContains(AccountWidget::class, $widgets);
        $this->assertNotContains(MembershipStatusWidget::class, $widgets);
    }

    public function test_membership_widget_shows_season(): void
    {
        $user = $this->actingAsMember();

        $season = TeamSeason::factory()->create(['team_id' => $this->team->id]);
        Membership::factory()->create([
            'team_id' => $this->team->id,
            'user_id' => $user->id,
            'team_season_id' => $season->id,
            'status' => MembershipStatusEnum::ACTIVE,
        ]);

        Livewire::test(MembershipStatusWidget::class)
            ->assertOk();
    }

    public function test_membership_page_bills_a_pending_membership_at_its_own_fee(): void
    {
        $this->travelTo('2026-11-15 10:00:00');

        $user = $this->actingAsMember();

        $bankTransfer = PaymentMethod::create([
            'method' => PaymentMethodEnum::BANK_TRANSFER,
            'title' => ['sk' => 'Bankovy prevod'],
            'is_active' => true,
            'sort_order' => 0,
        ]);
        $this->team->paymentMethods()->attach($bankTransfer->id, ['is_enabled' => true, 'sort_order' => 0]);
        $this->team->update(['bank_account_iban' => 'SK1234567890123456789012', 'bank_account_name' => 'BCZ Team']);

        // Prorating would charge 40.00 today; the membership was issued at 80.00.
        $season = TeamSeason::factory()->prorated()->create([
            'team_id' => $this->team->id,
            'starts_at' => '2026-09-01',
            'ends_at' => '2026-12-31',
            'fee_amount' => 80.00,
        ]);
        $membership = Membership::factory()->create([
            'team_id' => $this->team->id,
            'user_id' => $user->id,
            'team_season_id' => $season->id,
            'status' => MembershipStatusEnum::PENDING,
            'fee_amount' => 80.00,
            'fee_currency' => 'EUR',
            'starts_at' => '2026-09-01',
            'ends_at' => '2026-12-31',
        ]);

        Livewire::test(MemberMembership::class)
            ->assertOk()
            ->assertSee('80.00 EUR')
            ->assertDontSee('40.00 EUR');

        $this->assertEquals(80.00, (float) $membership->payments()->sole()->amount);
    }

    public function test_membership_widget_does_not_bill_a_member_with_a_free_membership(): void
    {
        $user = $this->actingAsMember();
        $user->update(['has_free_membership' => true]);

        TeamSeason::factory()->create([
            'team_id' => $this->team->id,
            'starts_at' => now()->subMonth(),
            'ends_at' => now()->addMonths(6),
            'fee_amount' => 80.00,
        ]);

        Livewire::test(MembershipStatusWidget::class)->assertOk();

        $membership = Membership::where('user_id', $user->id)->sole();
        $this->assertTrue($membership->is_free);
        $this->assertSame(MembershipStatusEnum::ACTIVE, $membership->status);
        $this->assertSame(0, $membership->payments()->count());
    }

    public function test_membership_widget_shows_no_membership(): void
    {
        $this->actingAsMember();

        Livewire::test(MembershipStatusWidget::class)
            ->assertOk();
    }

    public function test_member_pages_visible_for_member(): void
    {
        $this->actingAsMember();

        $this->assertTrue(MyTrainings::shouldRegisterNavigation());
        $this->assertTrue(MemberEvents::shouldRegisterNavigation());
        $this->assertTrue(MemberMembership::shouldRegisterNavigation());
        $this->assertTrue(MemberPayments::shouldRegisterNavigation());
    }

    public function test_member_pages_hidden_from_admin(): void
    {
        $this->actingAsAdmin();

        $this->assertFalse(MyTrainings::shouldRegisterNavigation());
        $this->assertFalse(MemberEvents::shouldRegisterNavigation());
        $this->assertFalse(MemberMembership::shouldRegisterNavigation());
        $this->assertFalse(MemberPayments::shouldRegisterNavigation());
    }
}
