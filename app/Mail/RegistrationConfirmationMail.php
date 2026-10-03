<?php

namespace App\Mail;

use App\Enums\PaymentStatusEnum;
use App\Models\Payment;
use App\Models\Team;
use App\Models\User;
use App\Notifications\Concerns\GeneratesPaymentQrCode;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\URL;

class RegistrationConfirmationMail extends Mailable implements ShouldQueue
{
    use GeneratesPaymentQrCode, Queueable, SerializesModels;

    public string $magicUrl;

    public ?string $teamName;

    public ?string $teamLogoUrl;

    public ?string $teamUrl;

    public ?string $teamEmail;

    public ?string $teamPhone;

    public ?string $teamWebsite;

    public ?string $paymentAmount = null;

    public ?string $paymentCurrency = null;

    public ?string $paymentUrl = null;

    public ?string $membershipPaymentUrl = null;

    /**
     * @param  string  $registrationKind  'training' or 'event' (translated via lang file)
     * @param  Payment|null  $membershipPayment  Outstanding club membership fee (membership-required
     *                                           trainings) — rendered with its QR code and pay link
     */
    public function __construct(
        public ?User $user,
        public string $registrationKind,
        public string $registrationTitle,
        public bool $isNewUser = false,
        public ?Team $team = null,
        public ?string $customContent = null,
        public ?Payment $payment = null,
        public ?Payment $membershipPayment = null,
    ) {
        $this->magicUrl = ($isNewUser && $user)
            ? URL::temporarySignedRoute('magic-login', now()->addDays(7), ['user' => $user->id])
            : '';

        $this->teamName = $this->team?->getTranslation('name', 'sk');
        $this->teamLogoUrl = $this->team?->getFirstMediaUrl('logo') ?: null;
        $teamSlug = $this->team?->slug;
        $this->teamUrl = $teamSlug ? url("/timy/{$teamSlug}") : url('/');
        $this->teamEmail = $this->team?->contact_email;
        $this->teamPhone = $this->team?->contact_phone;
        $this->teamWebsite = $this->team?->contact_website;

        if ($this->payment && $this->payment->status === PaymentStatusEnum::PENDING) {
            $symbol = $this->payment->currency === 'CZK' ? 'Kč' : '€';
            $this->paymentAmount = number_format((float) $this->payment->amount, 2, ',', ' ').' '.$symbol;
            $this->paymentCurrency = $this->payment->currency;
            $this->paymentUrl = URL::signedRoute('payment.page', ['payment' => $this->payment->id]);
        }

        if ($this->membershipPayment && $this->membershipPayment->status !== PaymentStatusEnum::PENDING) {
            $this->membershipPayment = null;
        }

        if ($this->membershipPayment) {
            $this->membershipPaymentUrl = URL::signedRoute('payment.page', ['payment' => $this->membershipPayment->id]);
        }
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: __('emails.registration_confirmation.subject', ['title' => $this->registrationTitle]),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.registration-confirmation',
            // Generated at send time (not stored on the queued mailable) so the raw
            // PNG bytes never end up in the serialized queue payload.
            with: [
                'qrCodeImage' => $this->qrCodeImageForPayment($this->membershipPayment),
            ],
        );
    }
}
