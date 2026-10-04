<?php

namespace Tests\Feature;

use App\Enums\MembershipStatusEnum;
use App\Enums\PaymentStatusEnum;
use App\Enums\RoleEnum;
use App\Enums\TrainingPricingTypeEnum;
use App\Filament\Resources\Teams\Pages\ViewTeam;
use App\Filament\Resources\Teams\RelationManagers\MembersRelationManager;
use App\Livewire\PaymentPage;
use App\Models\Membership;
use App\Models\Payment;
use App\Models\Team;
use App\Models\TeamSeason;
use App\Models\Training;
use App\Models\User;
use App\Services\GoPayService;
use App\Services\SeasonService;
use Filament\Actions\Testing\TestAction;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class SeasonBillingEdgeCasesTest extends TestCase
{
    use RefreshDatabase;

    private Team $team;

    private TeamSeason $autumn;

    protected function setUp(): void
    {
        parent::setUp();

        foreach (RoleEnum::cases() as $role) {
            Role::firstOrCreate(['name' => $role->value, 'guard_name' => 'web']);
        }

        $this->team = Team::factory()->create();
        $this->autumn = TeamSeason::factory()->create([
            'team_id' => $this->team->id,
            'starts_at' => '2026-09-01',
            'ends_at' => '2026-12-31',
            'fee_amount' => 80.00,
        ]);
    }

    private function athleteJoinedOn(string $date): User
    {
        $user = User::factory()->create();
        $user->assignRole(RoleEnum::CUSTOMER->value);
        $this->team->members()->attach($user, ['role' => RoleEnum::ATHLETE->value, 'is_active' => true, 'joined_at' => $date]);

        return $user;
    }

    public function test_a_returning_member_pays_from_the_month_they_come_back(): void
    {
        $this->travelTo('2026-11-02 10:00:00');

        $membership = app(SeasonService::class)->findOrCreateMembership($this->autumn, $this->athleteJoinedOn('2025-09-05'));

        $this->assertEquals(40.00, (float) $membership->fee_amount);
    }

    public function test_a_member_who_joins_in_october_pays_october_to_december(): void
    {
        $this->travelTo('2026-10-20 10:00:00');

        $membership = app(SeasonService::class)->findOrCreateMembership($this->autumn, $this->athleteJoinedOn('2026-10-20'));

        $this->assertEquals(60.00, (float) $membership->fee_amount);
    }

    public function test_a_member_who_joins_before_the_season_pays_the_full_fee(): void
    {
        $this->travelTo('2026-08-20 10:00:00');

        $membership = app(SeasonService::class)->findOrCreateMembership($this->autumn, $this->athleteJoinedOn('2026-08-20'));

        $this->assertEquals(80.00, (float) $membership->fee_amount);
    }

    public function test_a_season_created_mid_season_bills_earlier_members_in_full(): void
    {
        Notification::fake();
        $this->travelTo('2026-10-03 10:00:00');

        $team = Team::factory()->create(['membership_enabled' => true]);
        $earlier = User::factory()->create();
        $team->members()->attach($earlier, ['role' => RoleEnum::ATHLETE->value, 'is_active' => true, 'joined_at' => '2026-03-01']);

        $season = app(SeasonService::class)->createSeasonWithMemberships($team, [
            'name' => 'Jesenná sezóna 2026',
            'starts_at' => '2026-09-01',
            'ends_at' => '2026-12-31',
            'fee_amount' => 80.00,
            'fee_currency' => 'EUR',
            'payment_deadline_days' => 14,
        ]);

        $this->assertEquals(80.00, (float) $season->memberships()->sole()->fee_amount);
    }

    public function test_renewing_a_cancelled_membership_keeps_its_fee(): void
    {
        $this->travelTo('2026-11-10 10:00:00');

        $cancelled = Membership::factory()->create([
            'team_id' => $this->team->id,
            'team_season_id' => $this->autumn->id,
            'status' => MembershipStatusEnum::CANCELLED,
            'fee_amount' => 80.00,
        ]);

        $renewed = app(SeasonService::class)->renewMembership($cancelled);

        $this->assertEquals(80.00, (float) $renewed->fee_amount);
    }

    public function test_the_season_and_its_memberships_are_still_active_on_the_last_day(): void
    {
        $this->travelTo('2026-12-31 22:30:00');

        $member = $this->athleteJoinedOn('2026-09-01');
        Membership::factory()->create([
            'team_id' => $this->team->id,
            'user_id' => $member->id,
            'team_season_id' => $this->autumn->id,
            'status' => MembershipStatusEnum::ACTIVE,
            'starts_at' => '2026-09-01',
            'ends_at' => '2026-12-31',
        ]);

        $this->assertTrue($this->autumn->fresh()->isActive());
        $this->assertFalse($this->autumn->fresh()->isPast());
        $this->assertTrue($this->team->fresh()->currentSeason?->is($this->autumn));
        $this->assertTrue($member->hasActiveMembershipForTeam($this->team->id));
    }

    public function test_a_training_in_a_later_season_bills_that_season(): void
    {
        $this->travelTo('2026-12-20 10:00:00');

        $spring = TeamSeason::factory()->create([
            'team_id' => $this->team->id,
            'starts_at' => '2027-01-01',
            'ends_at' => '2027-06-30',
            'fee_amount' => 120.00,
        ]);
        $training = Training::factory()->create([
            'team_id' => $this->team->id,
            'team_season_id' => $spring->id,
            'pricing_type' => TrainingPricingTypeEnum::MEMBERSHIP_REQUIRED,
        ]);

        $this->assertTrue($training->membershipSeason()->is($spring));

        $user = User::factory()->create();
        Mail::fake();
        Notification::fake();

        Livewire::actingAs($user)
            ->test('training-registration-form', ['training' => $training])
            ->set('fields.meno', $user->first_name)
            ->set('fields.priezvisko', $user->last_name)
            ->set('gdprAgreed', true)
            ->call('submit');

        $membership = Membership::where('user_id', $user->id)->sole();
        $this->assertTrue($membership->season->is($spring));
        $this->assertEquals(120.00, (float) $membership->fee_amount);
    }

    public function test_add_membership_refuses_a_second_membership_for_the_same_season(): void
    {
        $member = $this->athleteJoinedOn('2026-09-01');
        app(SeasonService::class)->findOrCreateMembership($this->autumn, $member);

        $admin = User::factory()->create();
        $admin->assignRole(RoleEnum::SUPER_ADMIN);
        $this->actingAs($admin);
        Filament::setCurrentPanel(Filament::getPanel('admin'));
        Filament::setTenant($this->team);
        Filament::bootCurrentPanel();

        Livewire::test(MembersRelationManager::class, [
            'ownerRecord' => $this->team,
            'pageClass' => ViewTeam::class,
        ])->callAction(TestAction::make('addMembership')->table($member), [
            'team_season_id' => $this->autumn->id,
            'is_free' => false,
            'fee_amount' => '80',
            'fee_currency' => 'EUR',
        ]);

        $this->assertSame(1, Membership::where('user_id', $member->id)->count());
    }

    public function test_a_cancelled_payment_cannot_be_paid(): void
    {
        $payment = Payment::factory()->create(['status' => PaymentStatusEnum::CANCELLED]);

        $this->mock(GoPayService::class)->shouldNotReceive('createPayment');

        Livewire::test(PaymentPage::class, ['payment' => $payment])
            ->assertSet('isCompleted', true)
            ->assertSee('Platba bola zrušená')
            ->set('selectedMethod', 'gopay')
            ->call('pay')
            ->assertNoRedirect();
    }
}
