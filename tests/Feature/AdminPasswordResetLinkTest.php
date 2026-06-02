<?php

namespace Tests\Feature;

use App\Models\User;
use App\Notifications\ResetPassword;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use Tests\TestCase;

class AdminPasswordResetLinkTest extends TestCase
{
    use RefreshDatabase;

    public function test_reset_link_is_signed_and_accepted_by_the_reset_page(): void
    {
        $user = User::factory()->create();

        Notification::fake();

        $status = Password::sendResetLink(['email' => $user->email]);
        $this->assertSame(Password::RESET_LINK_SENT, $status);

        $resetUrl = null;
        Notification::assertSentTo($user, ResetPassword::class, function (ResetPassword $notification) use ($user, &$resetUrl): bool {
            $resetUrl = $notification->toMail($user)->viewData['resetUrl'];

            return true;
        });

        // The link must carry the signature the `signed` middleware verifies.
        $this->assertStringContainsString('signature=', $resetUrl);
        $this->assertStringContainsString('password-reset/reset', $resetUrl);

        // Visiting the signed link must not be rejected as an invalid signature.
        $response = $this->get($resetUrl);
        $response->assertStatus(200);
    }
}
