<?php

namespace Tests\Feature;

use App\Enums\InvitationStatusEnum;
use App\Enums\JoinRequestStatusEnum;
use App\Enums\TeamJoinModeEnum;
use App\Livewire\JoinTeam;
use App\Models\Team;
use App\Models\TeamInvitation;
use App\Models\TeamJoinRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class JoinTeamTest extends TestCase
{
    use RefreshDatabase;

    public function test_join_team_page_loads(): void
    {
        $response = $this->get('/pridaj-sa');

        $response->assertOk();
        $response->assertSeeLivewire('join-default-team');
    }

    public function test_team_search_returns_matching_teams(): void
    {
        $team = Team::factory()->create(['name' => ['sk' => 'BCZ Bratislava', 'en' => 'BCZ Bratislava'], 'is_active' => true]);
        Team::factory()->create(['name' => ['sk' => 'Workout Žilina', 'en' => 'Workout Žilina'], 'is_active' => true]);

        Livewire::test(JoinTeam::class)
            ->set('search', 'BCZ')
            ->assertSee('BCZ Bratislava')
            ->assertDontSee('Workout Žilina');
    }

    public function test_team_search_requires_minimum_characters(): void
    {
        Team::factory()->create(['name' => ['sk' => 'BCZ'], 'is_active' => true]);

        Livewire::test(JoinTeam::class)
            ->set('search', 'B')
            ->assertDontSee('BCZ');
    }

    public function test_team_search_excludes_inactive_teams(): void
    {
        Team::factory()->create(['name' => ['sk' => 'Inactive Team'], 'is_active' => false]);

        Livewire::test(JoinTeam::class)
            ->set('search', 'Inactive')
            ->assertDontSee('Inactive Team');
    }

    public function test_authenticated_user_can_send_join_request(): void
    {
        $user = User::factory()->create();
        $team = Team::factory()->create(['is_active' => true]);

        Livewire::actingAs($user)
            ->test(JoinTeam::class)
            ->call('selectTeam', $team->id)
            ->assertSet('requestSent', true);

        $this->assertDatabaseHas('team_join_requests', [
            'team_id' => $team->id,
            'user_id' => $user->id,
            'email' => $user->email,
            'status' => JoinRequestStatusEnum::Pending->value,
        ]);
    }

    public function test_guest_sees_request_form_when_clicking_join(): void
    {
        $team = Team::factory()->create(['is_active' => true]);

        Livewire::test(JoinTeam::class)
            ->call('selectTeam', $team->id)
            ->assertSet('showRequestForm', true)
            ->assertSet('selectedTeamId', $team->id);
    }

    public function test_guest_can_submit_join_request_with_name_and_email(): void
    {
        $team = Team::factory()->create(['is_active' => true]);

        Livewire::test(JoinTeam::class)
            ->call('selectTeam', $team->id)
            ->set('requestName', 'Ján Novák')
            ->set('requestEmail', 'jan@example.com')
            ->call('submitGuestRequest')
            ->assertSet('requestSent', true)
            ->assertSet('showRequestForm', false);

        $this->assertDatabaseHas('team_join_requests', [
            'team_id' => $team->id,
            'name' => 'Ján Novák',
            'email' => 'jan@example.com',
            'status' => JoinRequestStatusEnum::Pending->value,
        ]);
    }

    public function test_guest_request_validates_required_fields(): void
    {
        $team = Team::factory()->create(['is_active' => true]);

        Livewire::test(JoinTeam::class)
            ->call('selectTeam', $team->id)
            ->call('submitGuestRequest')
            ->assertHasErrors(['requestName', 'requestEmail']);
    }

    public function test_guest_request_validates_email_format(): void
    {
        $team = Team::factory()->create(['is_active' => true]);

        Livewire::test(JoinTeam::class)
            ->call('selectTeam', $team->id)
            ->set('requestName', 'Test')
            ->set('requestEmail', 'not-an-email')
            ->call('submitGuestRequest')
            ->assertHasErrors(['requestEmail']);
    }

    public function test_duplicate_join_request_shows_error(): void
    {
        $user = User::factory()->create();
        $team = Team::factory()->create(['is_active' => true]);

        TeamJoinRequest::factory()->create([
            'team_id' => $team->id,
            'email' => $user->email,
            'status' => JoinRequestStatusEnum::Pending,
        ]);

        Livewire::actingAs($user)
            ->test(JoinTeam::class)
            ->call('selectTeam', $team->id)
            ->assertSet('requestError', 'Žiadosť o pripojenie už bola odoslaná.');
    }

    public function test_existing_member_cannot_send_join_request(): void
    {
        $user = User::factory()->create();
        $team = Team::factory()->create(['is_active' => true]);
        $user->teams()->attach($team->id, ['is_active' => true, 'joined_at' => now()]);

        Livewire::actingAs($user)
            ->test(JoinTeam::class)
            ->call('selectTeam', $team->id)
            ->assertSet('requestError', 'Už ste členom tohto tímu.');
    }

    public function test_valid_invite_code_adds_authenticated_user_to_team(): void
    {
        $user = User::factory()->create();
        $team = Team::factory()->create(['is_active' => true]);
        $invitation = TeamInvitation::factory()->create([
            'team_id' => $team->id,
            'code' => 'TEST1234',
            'status' => InvitationStatusEnum::Pending,
            'invited_by' => User::factory()->create()->id,
        ]);

        Livewire::actingAs($user)
            ->test(JoinTeam::class)
            ->set('inviteCode', 'TEST1234')
            ->call('redeemCode')
            ->assertSet('codeSuccess', true);

        $this->assertTrue($team->members()->where('users.id', $user->id)->exists());
        $invitation->refresh();
        $this->assertEquals(InvitationStatusEnum::Accepted, $invitation->status);
    }

    public function test_invalid_invite_code_shows_error(): void
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(JoinTeam::class)
            ->set('inviteCode', 'INVALID')
            ->call('redeemCode')
            ->assertSet('codeError', 'Neplatný pozývací kód.');
    }

    public function test_empty_invite_code_shows_error(): void
    {
        Livewire::test(JoinTeam::class)
            ->call('redeemCode')
            ->assertSet('codeError', 'Zadajte pozývací kód.');
    }

    public function test_expired_invite_code_shows_error(): void
    {
        $user = User::factory()->create();
        $team = Team::factory()->create(['is_active' => true]);
        TeamInvitation::factory()->expired()->create([
            'team_id' => $team->id,
            'code' => 'EXPIRED1',
            'invited_by' => User::factory()->create()->id,
        ]);

        Livewire::actingAs($user)
            ->test(JoinTeam::class)
            ->set('inviteCode', 'EXPIRED1')
            ->call('redeemCode')
            ->assertSet('codeError', 'Tento pozývací kód už nie je platný.');
    }

    public function test_already_member_cannot_use_invite_code(): void
    {
        $user = User::factory()->create();
        $team = Team::factory()->create(['is_active' => true]);
        $user->teams()->attach($team->id, ['is_active' => true, 'joined_at' => now()]);

        TeamInvitation::factory()->create([
            'team_id' => $team->id,
            'code' => 'MEMBER01',
            'status' => InvitationStatusEnum::Pending,
            'invited_by' => User::factory()->create()->id,
        ]);

        Livewire::actingAs($user)
            ->test(JoinTeam::class)
            ->set('inviteCode', 'MEMBER01')
            ->call('redeemCode')
            ->assertSet('codeError', 'Už ste členom tohto tímu.');
    }

    public function test_guest_with_invite_code_is_redirected_to_register(): void
    {
        $team = Team::factory()->create(['is_active' => true]);
        TeamInvitation::factory()->create([
            'team_id' => $team->id,
            'code' => 'GUEST123',
            'status' => InvitationStatusEnum::Pending,
            'invited_by' => User::factory()->create()->id,
        ]);

        Livewire::test(JoinTeam::class)
            ->set('inviteCode', 'GUEST123')
            ->call('redeemCode')
            ->assertRedirect(route('register'));
    }

    public function test_search_resets_request_state(): void
    {
        $team = Team::factory()->create(['is_active' => true]);

        Livewire::test(JoinTeam::class)
            ->call('selectTeam', $team->id)
            ->assertSet('showRequestForm', true)
            ->set('search', 'New search')
            ->assertSet('showRequestForm', false)
            ->assertSet('selectedTeamId', null);
    }

    public function test_open_join_mode_adds_user_directly(): void
    {
        $user = User::factory()->create();
        $team = Team::factory()->create([
            'is_active' => true,
            'join_mode' => TeamJoinModeEnum::OPEN,
        ]);

        Livewire::actingAs($user)
            ->test(JoinTeam::class)
            ->call('selectTeam', $team->id)
            ->assertSet('joinedDirectly', true)
            ->assertSet('requestSent', false);

        $this->assertTrue($team->members()->where('users.id', $user->id)->exists());
        $this->assertDatabaseMissing('team_join_requests', [
            'team_id' => $team->id,
            'user_id' => $user->id,
        ]);
    }

    public function test_approval_join_mode_creates_join_request(): void
    {
        $user = User::factory()->create();
        $team = Team::factory()->create([
            'is_active' => true,
            'join_mode' => TeamJoinModeEnum::APPROVAL,
        ]);

        Livewire::actingAs($user)
            ->test(JoinTeam::class)
            ->call('selectTeam', $team->id)
            ->assertSet('requestSent', true)
            ->assertSet('joinedDirectly', false);

        $this->assertFalse($team->members()->where('users.id', $user->id)->exists());
        $this->assertDatabaseHas('team_join_requests', [
            'team_id' => $team->id,
            'user_id' => $user->id,
            'status' => JoinRequestStatusEnum::Pending->value,
        ]);
    }

    public function test_open_join_mode_prevents_duplicate_membership(): void
    {
        $user = User::factory()->create();
        $team = Team::factory()->create([
            'is_active' => true,
            'join_mode' => TeamJoinModeEnum::OPEN,
        ]);
        $user->teams()->attach($team->id, ['is_active' => true, 'joined_at' => now()]);

        Livewire::actingAs($user)
            ->test(JoinTeam::class)
            ->call('selectTeam', $team->id)
            ->assertSet('requestError', 'Už ste členom tohto tímu.');
    }

    public function test_autofills_user_data_when_authenticated(): void
    {
        $user = User::factory()->create([
            'first_name' => 'Test',
            'last_name' => 'User',
            'email' => 'test@example.com',
        ]);

        Livewire::actingAs($user)
            ->test(JoinTeam::class)
            ->assertSet('requestName', 'Test User')
            ->assertSet('requestEmail', 'test@example.com');
    }
}
