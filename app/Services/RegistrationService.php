<?php

namespace App\Services;

use App\Mail\RegistrationConfirmationMail;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class RegistrationService
{
    /**
     * Resolve an existing user by email or create a new one.
     *
     * @return array{user: User, created: bool}
     */
    public static function resolveOrCreateUser(string $email, ?string $name = null): array
    {
        $user = User::where('email', $email)->first();

        if ($user) {
            return ['user' => $user, 'created' => false];
        }

        $user = User::create([
            'name' => $name ?? Str::before($email, '@'),
            'email' => $email,
            'password' => Str::random(32),
            'email_verified_at' => now(),
        ]);

        return ['user' => $user, 'created' => true];
    }

    /**
     * Send registration confirmation with magic login link.
     */
    public static function sendConfirmation(User $user, string $registrationType, string $registrationTitle, bool $isNewUser = false): void
    {
        Mail::to($user->email)->queue(
            new RegistrationConfirmationMail(
                user: $user,
                registrationType: $registrationType,
                registrationTitle: $registrationTitle,
                isNewUser: $isNewUser,
            ),
        );
    }

    /**
     * Extract email from form data using the registration schema.
     *
     * @param  array<string, mixed>  $formData
     * @param  list<array<string, mixed>>  $schema
     */
    public static function extractEmailFromFormData(array $formData, array $schema): ?string
    {
        foreach ($schema as $field) {
            if (($field['type'] ?? '') === 'email' && ! empty($formData[$field['name']] ?? null)) {
                return $formData[$field['name']];
            }
        }

        return null;
    }

    /**
     * Extract a name from form data (first text_input field).
     *
     * @param  array<string, mixed>  $formData
     * @param  list<array<string, mixed>>  $schema
     */
    public static function extractNameFromFormData(array $formData, array $schema): ?string
    {
        foreach ($schema as $field) {
            if (($field['type'] ?? '') === 'text_input' && ! empty($formData[$field['name']] ?? null)) {
                return $formData[$field['name']];
            }
        }

        return null;
    }
}
