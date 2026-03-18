<?php

namespace Tests\Feature;

use App\Enums\MembershipStatusEnum;
use App\Enums\RoleEnum;
use App\Models\Membership;
use App\Models\Team;
use App\Models\TeamSeason;
use App\Models\User;
use App\Notifications\MembershipRenewalReminder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class MembershipRenewalReminderTest extends TestCase
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

    public function test_sends_reminders_for_upcoming_season(): void
    {
        Notification::fake();

        $season = TeamSeason::factory()->create([
            'team_id' => $this->team->id,
            'starts_at' => now()->addDays(10),
            'ends_at' => now()->addDays(10)->addMonths(9),
        ]);

        $member = User::factory()->create();
        $member->teams()->attach($this->team, [
            'role' => RoleEnum::ATHLETE->value,
            'is_active' => true,
        ]);

        $this->artisan('memberships:send-renewal-reminders')
            ->assertSuccessful();

        Notification::assertSentTo($member, MembershipRenewalReminder::class);

        $season->refresh();
        $this->assertNotNull($season->renewal_notified_at);
    }

    public function test_does_not_send_to_members_with_active_membership(): void
    {
        Notification::fake();

        $season = TeamSeason::factory()->create([
            'team_id' => $this->team->id,
            'starts_at' => now()->addDays(10),
            'ends_at' => now()->addDays(10)->addMonths(9),
        ]);

        $member = User::factory()->create();
        $member->teams()->attach($this->team, [
            'role' => RoleEnum::ATHLETE->value,
            'is_active' => true,
        ]);

        Membership::create([
            'team_id' => $this->team->id,
            'user_id' => $member->id,
            'team_season_id' => $season->id,
            'status' => MembershipStatusEnum::ACTIVE,
            'starts_at' => $season->starts_at,
            'ends_at' => $season->ends_at,
            'fee_amount' => 50.00,
            'fee_currency' => 'EUR',
        ]);

        $this->artisan('memberships:send-renewal-reminders')
            ->assertSuccessful();

        Notification::assertNotSentTo($member, MembershipRenewalReminder::class);
    }

    public function test_does_not_send_for_already_notified_season(): void
    {
        Notification::fake();

        $season = TeamSeason::factory()->create([
            'team_id' => $this->team->id,
            'starts_at' => now()->addDays(10),
            'ends_at' => now()->addDays(10)->addMonths(9),
            'renewal_notified_at' => now()->subDay(),
        ]);

        $member = User::factory()->create();
        $member->teams()->attach($this->team, [
            'role' => RoleEnum::ATHLETE->value,
            'is_active' => true,
        ]);

        $this->artisan('memberships:send-renewal-reminders')
            ->assertSuccessful();

        Notification::assertNotSentTo($member, MembershipRenewalReminder::class);
    }

    public function test_does_not_send_for_season_more_than_two_weeks_away(): void
    {
        Notification::fake();

        TeamSeason::factory()->create([
            'team_id' => $this->team->id,
            'starts_at' => now()->addWeeks(3),
            'ends_at' => now()->addWeeks(3)->addMonths(9),
        ]);

        $member = User::factory()->create();
        $member->teams()->attach($this->team, [
            'role' => RoleEnum::ATHLETE->value,
            'is_active' => true,
        ]);

        $this->artisan('memberships:send-renewal-reminders')
            ->assertSuccessful();

        Notification::assertNotSentTo($member, MembershipRenewalReminder::class);
    }
}
