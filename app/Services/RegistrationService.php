<?php

namespace App\Services;

use App\Enums\RegistrationStatusEnum;
use App\Enums\TrainingPricingTypeEnum;
use App\Mail\RegistrationConfirmationMail;
use App\Models\Team;
use App\Models\Training;
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

        $fallback = $name ?? Str::before($email, '@');
        $parts = explode(' ', $fallback, 2);

        $user = User::create([
            'first_name' => $parts[0],
            'last_name' => $parts[1] ?? '',
            'email' => $email,
            'password' => Str::random(32),
            'email_verified_at' => now(),
        ]);

        return ['user' => $user, 'created' => true];
    }

    /**
     * Send registration confirmation with magic login link.
     *
     * @param  array<string, list<array<string, mixed>>>|null  $customEmailContent  Locale-keyed Mason brick content
     */
    public static function sendConfirmation(User $user, string $registrationType, string $registrationTitle, bool $isNewUser = false, ?Team $team = null, ?array $customEmailContent = null, ?string $locale = null): void
    {
        $resolvedLocale = $locale ?? $user->locale ?? app()->getLocale() ?? 'sk';
        $bricks = $customEmailContent[$resolvedLocale] ?? $customEmailContent['sk'] ?? null;

        $customHtml = null;
        if (! empty($bricks)) {
            $customHtml = EmailService::renderBricks($bricks);
        }

        Mail::to($user->email)->queue(
            new RegistrationConfirmationMail(
                user: $user,
                registrationType: $registrationType,
                registrationTitle: $registrationTitle,
                isNewUser: $isNewUser,
                team: $team,
                customContent: $customHtml,
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
        $firstName = null;
        $lastName = null;

        foreach ($schema as $field) {
            $type = $field['type'] ?? '';
            $value = $formData[$field['name']] ?? null;

            if (! empty($value)) {
                if ($type === 'full_name') {
                    return $value;
                }
                if ($type === 'first_name') {
                    $firstName = $value;
                }
                if ($type === 'last_name') {
                    $lastName = $value;
                }
            }
        }

        if ($firstName || $lastName) {
            return trim(($firstName ?? '').' '.($lastName ?? ''));
        }

        // Fallback: first text_input field
        foreach ($schema as $field) {
            if (($field['type'] ?? '') === 'text_input' && ! empty($formData[$field['name']] ?? null)) {
                return $formData[$field['name']];
            }
        }

        return null;
    }

    /**
     * Extract phone from form data using the registration schema.
     *
     * @param  array<string, mixed>  $formData
     * @param  list<array<string, mixed>>  $schema
     */
    public static function extractPhoneFromFormData(array $formData, array $schema): ?string
    {
        foreach ($schema as $field) {
            if (($field['type'] ?? '') === 'phone' && ! empty($formData[$field['name']] ?? null)) {
                return $formData[$field['name']];
            }
        }

        return null;
    }

    /**
     * Extract birth_date from form data using the registration schema.
     *
     * @param  array<string, mixed>  $formData
     * @param  list<array<string, mixed>>  $schema
     */
    public static function extractBirthDateFromFormData(array $formData, array $schema): ?string
    {
        foreach ($schema as $field) {
            if (($field['type'] ?? '') === 'birth_date' && ! empty($formData[$field['name']] ?? null)) {
                return $formData[$field['name']];
            }
        }

        return null;
    }

    /**
     * Extract gender from form data using the registration schema.
     *
     * @param  array<string, mixed>  $formData
     * @param  list<array<string, mixed>>  $schema
     */
    public static function extractGenderFromFormData(array $formData, array $schema): ?string
    {
        foreach ($schema as $field) {
            if (($field['type'] ?? '') === 'gender' && ! empty($formData[$field['name']] ?? null)) {
                return $formData[$field['name']];
            }
        }

        return null;
    }

    /**
     * Determine the registration status based on training pricing and user membership.
     */
    public static function determineRegistrationStatus(Training $training, ?User $user): RegistrationStatusEnum
    {
        if ($training->pricing_type === TrainingPricingTypeEnum::FREE) {
            return RegistrationStatusEnum::Approved;
        }

        if ($training->pricing_type === TrainingPricingTypeEnum::MEMBERSHIP_REQUIRED && $user?->hasActiveMembershipForTeam($training->team_id)) {
            return RegistrationStatusEnum::Approved;
        }

        return RegistrationStatusEnum::Pending;
    }

    /**
     * Determine the post-registration UI state for the frontend.
     *
     * @return 'free_approved'|'membership_valid'|'membership_needed'|'payment_needed'
     */
    public static function determinePostRegistrationState(Training $training, ?User $user): string
    {
        if ($training->pricing_type === TrainingPricingTypeEnum::FREE) {
            return 'free_approved';
        }

        if ($training->pricing_type === TrainingPricingTypeEnum::MEMBERSHIP_REQUIRED) {
            if ($user?->hasActiveMembershipForTeam($training->team_id)) {
                return 'membership_valid';
            }

            return 'membership_needed';
        }

        return 'payment_needed';
    }
}
