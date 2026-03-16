<?php

namespace Tests\Feature;

use App\Enums\InvitationStatusEnum;
use App\Enums\RoleEnum;
use App\Mail\TeamInvitationMail;
use App\Models\Team;
use App\Models\TeamInvitation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class TeamInvitationTest extends TestCase
{
    use RefreshDatabase;

    public function test_existing_user_can_accept_invitation_via_signed_url(): void
    {
        Role::findOrCreate(RoleEnum::ATHLETE->value, 'web');
        Role::findOrCreate(RoleEnum::CUSTOMER->value, 'web');

        $team = Team::factory()->create();
        $user = User::factory()->create();
        $invitation = TeamInvitation::factory()->create([
            'team_id' => $team->id,
            'email' => $user->email,
            'invited_by' => User::factory()->create()->id,
        ]);

        $url = URL::temporarySignedRoute(
            'team-invitations.accept',
            $invitation->expires_at,
            ['invitation' => $invitation->id],
        );

        $response = $this->get($url);

        $response->assertRedirect('/admin');
        $this->assertTrue($team->members()->where('users.id', $user->id)->exists());
        $this->assertAuthenticatedAs($user);

        // Team-scoped ATHLETE role is on pivot, not Spatie
        $this->assertTrue(
            $user->fresh()->teams()
                ->where('teams.id', $team->id)
                ->wherePivot('role', RoleEnum::ATHLETE->value)
                ->exists()
        );

        $invitation->refresh();
        $this->assertEquals(InvitationStatusEnum::Accepted, $invitation->status);
        $this->assertNotNull($invitation->accepted_at);
    }

    public function test_new_user_can_register_via_invitation(): void
    {
        Role::findOrCreate(RoleEnum::ATHLETE->value, 'web');
        Role::findOrCreate(RoleEnum::CUSTOMER->value, 'web');

        $team = Team::factory()->create();
        $invitation = TeamInvitation::factory()->create([
            'team_id' => $team->id,
            'email' => 'newuser@example.com',
            'invited_by' => User::factory()->create()->id,
        ]);

        $url = URL::temporarySignedRoute(
            'team-invitations.register',
            $invitation->expires_at,
            ['invitation' => $invitation->id],
        );

        $response = $this->get($url);
        $response->assertOk();
        $response->assertSee('newuser@example.com');

        $postUrl = URL::temporarySignedRoute(
            'team-invitations.register',
            $invitation->expires_at,
            ['invitation' => $invitation->id],
        );

        $response = $this->post($postUrl, [
            'first_name' => 'Test',
            'last_name' => 'User',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect('/admin');

        $user = User::where('email', 'newuser@example.com')->first();
        $this->assertNotNull($user);
        $this->assertEquals('Test', $user->first_name);
        $this->assertEquals('User', $user->last_name);
        $this->assertTrue($team->members()->where('users.id', $user->id)->exists());
        // Global role is CUSTOMER, team-scoped ATHLETE on pivot
        $this->assertTrue($user->hasRole(RoleEnum::CUSTOMER));
        $this->assertTrue(
            $user->teams()
                ->where('teams.id', $team->id)
                ->wherePivot('role', RoleEnum::ATHLETE->value)
                ->exists()
        );
        $this->assertAuthenticatedAs($user);

        $invitation->refresh();
        $this->assertEquals(InvitationStatusEnum::Accepted, $invitation->status);
    }

    public function test_expired_invitation_cannot_be_accepted(): void
    {
        $user = User::factory()->create();
        $invitation = TeamInvitation::factory()->expired()->create([
            'email' => $user->email,
            'invited_by' => User::factory()->create()->id,
        ]);

        $url = URL::temporarySignedRoute(
            'team-invitations.accept',
            now()->addDay(),
            ['invitation' => $invitation->id],
        );

        $response = $this->get($url);

        $response->assertForbidden();
    }

    public function test_accepted_invitation_cannot_be_used_again(): void
    {
        $user = User::factory()->create();
        $invitation = TeamInvitation::factory()->accepted()->create([
            'email' => $user->email,
            'invited_by' => User::factory()->create()->id,
        ]);

        $url = URL::temporarySignedRoute(
            'team-invitations.accept',
            now()->addDay(),
            ['invitation' => $invitation->id],
        );

        $response = $this->get($url);

        $response->assertForbidden();
    }

    public function test_unsigned_url_is_rejected(): void
    {
        $invitation = TeamInvitation::factory()->create([
            'invited_by' => User::factory()->create()->id,
        ]);

        $response = $this->get("/team-invitations/{$invitation->id}/accept");

        $response->assertForbidden();
    }

    public function test_registration_validates_required_fields(): void
    {
        $invitation = TeamInvitation::factory()->create([
            'invited_by' => User::factory()->create()->id,
        ]);

        $url = URL::temporarySignedRoute(
            'team-invitations.register',
            $invitation->expires_at,
            ['invitation' => $invitation->id],
        );

        $response = $this->post($url, []);

        $response->assertSessionHasErrors(['first_name', 'last_name', 'password']);
    }

    public function test_expire_command_marks_expired_invitations(): void
    {
        $expired = TeamInvitation::factory()->create([
            'expires_at' => now()->subHour(),
            'invited_by' => User::factory()->create()->id,
        ]);

        $pending = TeamInvitation::factory()->create([
            'expires_at' => now()->addDay(),
            'invited_by' => User::factory()->create()->id,
        ]);

        $this->artisan('team-invitations:expire')
            ->assertSuccessful();

        $expired->refresh();
        $pending->refresh();

        $this->assertEquals(InvitationStatusEnum::Expired, $expired->status);
        $this->assertEquals(InvitationStatusEnum::Pending, $pending->status);
    }

    public function test_team_invitation_mail_uses_correct_route_for_existing_user(): void
    {
        Mail::fake();

        $user = User::factory()->create();
        $invitation = TeamInvitation::factory()->create([
            'email' => $user->email,
            'invited_by' => User::factory()->create()->id,
        ]);

        $mail = new TeamInvitationMail($invitation);

        $this->assertStringContains('team-invitations/'.$invitation->id.'/accept', $mail->acceptUrl);
    }

    public function test_team_invitation_mail_uses_register_route_for_new_user(): void
    {
        Mail::fake();

        $invitation = TeamInvitation::factory()->create([
            'email' => 'brand-new@example.com',
            'invited_by' => User::factory()->create()->id,
        ]);

        $mail = new TeamInvitationMail($invitation);

        $this->assertStringContains('team-invitations/'.$invitation->id.'/register', $mail->acceptUrl);
    }

    private function assertStringContains(string $needle, string $haystack): void
    {
        $this->assertTrue(
            str_contains($haystack, $needle),
            "Failed asserting that '{$haystack}' contains '{$needle}'."
        );
    }
}
