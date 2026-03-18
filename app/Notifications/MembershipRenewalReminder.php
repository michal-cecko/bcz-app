<?php

namespace App\Notifications;

use App\Models\TeamSeason;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class MembershipRenewalReminder extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public TeamSeason $season,
        public string $paymentUrl,
    ) {}

    /** @return list<string> */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $user = $notifiable;
        $teamName = $this->season->team->getTranslation('name', 'sk');
        $seasonName = $this->season->name;
        $feeAmount = number_format((float) $this->season->fee_amount, 2);
        $feeCurrency = $this->season->fee_currency;

        return (new MailMessage)
            ->subject('Členstvo v tíme '.$teamName.' — sezóna '.$seasonName)
            ->view('emails.membership-renewal-reminder', [
                'user' => $user,
                'season' => $this->season,
                'paymentUrl' => $this->paymentUrl,
                'teamName' => $teamName,
                'seasonName' => $seasonName,
                'feeAmount' => $feeAmount,
                'feeCurrency' => $feeCurrency,
                'emailSubject' => 'Členstvo — nová sezóna '.$seasonName,
                'teamLogoUrl' => null,
                'teamUrl' => null,
                'teamEmail' => null,
                'teamPhone' => null,
                'teamWebsite' => null,
            ]);
    }

    /** @return array<string, mixed> */
    public function toArray(object $notifiable): array
    {
        return [
            'team_season_id' => $this->season->id,
            'season_name' => $this->season->name,
            'team_name' => $this->season->team->getTranslation('name', 'sk'),
            'fee_amount' => $this->season->fee_amount,
            'fee_currency' => $this->season->fee_currency,
            'deadline' => $this->season->starts_at->toDateString(),
            'type' => 'membership_renewal_reminder',
        ];
    }
}
