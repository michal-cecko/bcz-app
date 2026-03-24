<?php

namespace App\Notifications;

use App\Enums\PaymentMethodEnum;
use App\Models\Payment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PaymentConfirmed extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Payment $payment,
    ) {}

    /** @return list<string> */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $user = $notifiable;
        $team = $this->payment->team;
        $itemTitle = $this->resolveItemTitle();
        $itemType = $this->resolveItemType();

        return (new MailMessage)
            ->subject('Platba potvrdená — '.$itemTitle)
            ->view('emails.payment-confirmed', [
                'user' => $user,
                'payment' => $this->payment,
                'itemTitle' => $itemTitle,
                'itemType' => $itemType,
                'paymentMethodLabel' => $this->resolvePaymentMethodLabel(),
                'emailSubject' => 'Platba potvrdená',
                'teamName' => $team?->getTranslation('name', 'sk'),
                'teamLogoUrl' => $team?->getFirstMediaUrl('logo') ?: null,
                'teamUrl' => $team ? url('/timy/'.$team->slug) : null,
                'teamEmail' => $team?->contact_email,
                'teamPhone' => $team?->contact_phone,
                'teamWebsite' => $team?->contact_website,
            ]);
    }

    /** @return array<string, mixed> */
    public function toArray(object $notifiable): array
    {
        return [
            'payment_id' => $this->payment->id,
            'payable_type' => $this->payment->payable_type,
            'amount' => $this->payment->amount,
            'currency' => $this->payment->currency,
            'type' => 'payment_confirmed',
        ];
    }

    private function resolveItemTitle(): string
    {
        $payable = $this->payment->payable;

        if (! $payable) {
            return 'Platba #'.$this->payment->id;
        }

        return match ($this->payment->payable_type) {
            'membership' => $payable->season?->name ?? 'Členstvo',
            'training_registration' => $payable->training?->getTranslation('title', 'sk') ?? 'Tréning',
            default => 'Platba',
        };
    }

    private function resolveItemType(): string
    {
        return match ($this->payment->payable_type) {
            'membership' => 'Členstvo',
            'training_registration' => 'Tréning',
            'competition_registration' => 'Súťaž',
            'event_registration' => 'Podujatie',
            default => 'Platba',
        };
    }

    private function resolvePaymentMethodLabel(): string
    {
        return match ($this->payment->payment_method) {
            PaymentMethodEnum::GOPAY => 'GoPay (kartou)',
            PaymentMethodEnum::BANK_TRANSFER => 'Bankový prevod',
            PaymentMethodEnum::CASH => 'Hotovosť',
            default => '-',
        };
    }
}
