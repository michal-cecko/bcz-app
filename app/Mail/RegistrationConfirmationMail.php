<?php

namespace App\Mail;

use App\Models\Team;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\URL;

class RegistrationConfirmationMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public string $magicUrl;

    public string $emailSubject;

    public ?string $teamName;

    public ?string $teamLogoUrl;

    public ?string $teamUrl;

    public ?string $teamEmail;

    public ?string $teamPhone;

    public ?string $teamWebsite;

    public function __construct(
        public User $user,
        public string $registrationType,
        public string $registrationTitle,
        public bool $isNewUser = false,
        public ?Team $team = null,
        public ?string $customContent = null,
    ) {
        $this->magicUrl = $isNewUser
            ? URL::temporarySignedRoute('magic-login', now()->addDays(7), ['user' => $user->id])
            : '';

        $this->emailSubject = $this->isNewUser
            ? "Váš účet bol vytvorený — {$this->registrationTitle}"
            : "Potvrdenie registrácie — {$this->registrationTitle}";

        $this->teamName = $this->team?->getTranslation('name', 'sk');
        $this->teamLogoUrl = $this->team?->getFirstMediaUrl('logo') ?: null;
        $teamSlug = $this->team?->slug;
        $this->teamUrl = $teamSlug ? url("/timy/{$teamSlug}") : url('/');
        $this->teamEmail = $this->team?->contact_email;
        $this->teamPhone = $this->team?->contact_phone;
        $this->teamWebsite = $this->team?->contact_website;
    }

    public function envelope(): Envelope
    {
        return new Envelope(subject: $this->emailSubject);
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.registration-confirmation',
        );
    }
}
