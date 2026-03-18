<?php

namespace App\Mail;

use App\Models\TeamInvitation;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\URL;

class TeamInvitationMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public string $acceptUrl;

    public string $emailSubject;

    public ?string $teamName;

    public ?string $teamLogoUrl;

    public ?string $teamUrl;

    public ?string $teamEmail;

    public ?string $teamPhone;

    public ?string $teamWebsite;

    public function __construct(public TeamInvitation $invitation)
    {
        $existingUser = User::where('email', $invitation->email)->exists();

        $routeName = $existingUser
            ? 'team-invitations.accept'
            : 'team-invitations.register';

        $this->acceptUrl = URL::temporarySignedRoute(
            $routeName,
            $invitation->expires_at,
            ['invitation' => $invitation->id],
        );

        $team = $invitation->team;
        $this->teamName = $team->getTranslation('name', 'sk');
        $this->emailSubject = "Pozvánka do tímu {$this->teamName}";
        $this->teamLogoUrl = $team->getFirstMediaUrl('logo') ?: null;
        $teamSlug = $team->slug;
        $this->teamUrl = $teamSlug ? url("/timy/{$teamSlug}") : url('/');
        $this->teamEmail = $team->contact_email;
        $this->teamPhone = $team->contact_phone;
        $this->teamWebsite = $team->contact_website;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->emailSubject,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.team-invitation',
        );
    }
}
