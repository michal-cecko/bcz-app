<?php

namespace App\Mail;

use App\Models\Team;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AdminEmail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $emailSubject,
        public string $emailBody,
        public ?Team $team = null,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->emailSubject,
        );
    }

    public function content(): Content
    {
        $teamName = $this->team?->getTranslation('name', 'sk');
        $teamLogoUrl = $this->team?->getFirstMediaUrl('logo') ?: null;
        $teamSlug = $this->team?->slug;
        $teamUrl = $teamSlug ? url("/timy/{$teamSlug}") : url('/');

        return new Content(
            view: 'emails.admin-email',
            with: [
                'emailSubject' => $this->emailSubject,
                'emailBody' => $this->emailBody,
                'teamName' => $teamName,
                'teamLogoUrl' => $teamLogoUrl,
                'teamUrl' => $teamUrl,
                'teamEmail' => $this->team?->contact_email,
                'teamPhone' => $this->team?->contact_phone,
                'teamWebsite' => $this->team?->contact_website,
            ],
        );
    }
}
