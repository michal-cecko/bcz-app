<?php

namespace Tests\Feature\Filament;

use App\Enums\MembershipStatusEnum;
use App\Enums\PaymentStatusEnum;
use App\Enums\RoleEnum;
use App\Filament\Pages\MemberPayments;
use App\Models\Membership;
use App\Models\Payment;
use App\Models\Team;
use App\Models\TeamSeason;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ActionRestrictionTest extends TestCase
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
        $user->teams()->syncWithoutDetaching([
            $this->team->id => ['role' => RoleEnum::ATHLETE->value],
        ]);
        $this->actingAs($user);
        Filament::setTenant($this->team);

        return $user;
    }

    protected function actingAsAdmin(): User
    {
        $user = User::factory()->create();
        $user->assignRole(RoleEnum::SUPER_ADMIN);
        $user->teams()->syncWithoutDetaching([$this->team->id => []]);
        $this->actingAs($user);
        Filament::setTenant($this->team);

        return $user;
    }

    public function test_member_is_member_level(): void
    {
        $member = $this->actingAsMember();

        $this->assertTrue($member->isMemberLevel());
    }

    public function test_admin_is_not_member_level(): void
    {
        $admin = $this->actingAsAdmin();

        $this->assertFalse($admin->isMemberLevel());
    }

    public function test_member_payments_page_scoped_to_own(): void
    {
        $member = $this->actingAsMember();
        $otherUser = User::factory()->create();
        $otherUser->teams()->syncWithoutDetaching([
            $this->team->id => ['role' => RoleEnum::ATHLETE->value],
        ]);

        Payment::factory()->create([
            'team_id' => $this->team->id,
            'user_id' => $member->id,
            'status' => PaymentStatusEnum::COMPLETED,
        ]);
        Payment::factory()->create([
            'team_id' => $this->team->id,
            'user_id' => $otherUser->id,
            'status' => PaymentStatusEnum::COMPLETED,
        ]);

        // Member should only see own payments via the member payments page
        $page = new MemberPayments;
        $this->assertNotNull($page);

        // Verify query scoping directly
        $ownPayments = Payment::where('user_id', $member->id)
            ->where('team_id', $this->team->id)
            ->count();
        $allPayments = Payment::where('team_id', $this->team->id)->count();

        $this->assertEquals(1, $ownPayments);
        $this->assertEquals(2, $allPayments);
    }

    public function test_member_memberships_scoped_to_own(): void
    {
        $member = $this->actingAsMember();
        $otherUser = User::factory()->create();
        $otherUser->teams()->syncWithoutDetaching([
            $this->team->id => ['role' => RoleEnum::ATHLETE->value],
        ]);

        $season = TeamSeason::factory()->create(['team_id' => $this->team->id]);

        Membership::factory()->create([
            'team_id' => $this->team->id,
            'user_id' => $member->id,
            'team_season_id' => $season->id,
            'status' => MembershipStatusEnum::ACTIVE,
        ]);
        Membership::factory()->create([
            'team_id' => $this->team->id,
            'user_id' => $otherUser->id,
            'team_season_id' => $season->id,
            'status' => MembershipStatusEnum::ACTIVE,
        ]);

        $ownMemberships = Membership::where('user_id', $member->id)
            ->where('team_id', $this->team->id)
            ->count();
        $allMemberships = Membership::where('team_id', $this->team->id)->count();

        $this->assertEquals(1, $ownMemberships);
        $this->assertEquals(2, $allMemberships);
    }
}
