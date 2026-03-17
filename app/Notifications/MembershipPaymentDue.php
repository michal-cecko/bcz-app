<?php

namespace App\Notifications;

use App\Models\Membership;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class MembershipPaymentDue extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Membership $membership,
    ) {}

    /** @return list<string> */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $teamName = $this->membership->team?->getTranslation('name', 'sk') ?? '';
        $seasonName = $this->membership->season?->name ?? '';
        $feeAmount = number_format((float) $this->membership->fee_amount, 2).' '.$this->membership->fee_currency;
        $deadline = $this->membership->payment_deadline_at?->format('d.m.Y') ?? '';

        return (new MailMessage)
            ->subject("Platba za členstvo — {$seasonName}")
            ->greeting('Dobry den,')
            ->line("Bolo vam vytvorene clenstvo v time **{$teamName}** pre sezonu **{$seasonName}**.")
            ->line("Suma: **{$feeAmount}**")
            ->line("Splatnost do: **{$deadline}**")
            ->line('Prosim, uhradte platbu co najskor. Po uplynuti terminu bude clenstvo automaticky zrusene.')
            ->salutation('S pozdravom, tim BCZ');
    }

    /** @return array<string, mixed> */
    public function toArray(object $notifiable): array
    {
        return [
            'membership_id' => $this->membership->id,
            'team_name' => $this->membership->team?->getTranslation('name', 'sk'),
            'season_name' => $this->membership->season?->name,
            'fee_amount' => $this->membership->fee_amount,
            'fee_currency' => $this->membership->fee_currency,
            'payment_deadline_at' => $this->membership->payment_deadline_at?->toIso8601String(),
            'type' => 'membership_payment_due',
        ];
    }
}
