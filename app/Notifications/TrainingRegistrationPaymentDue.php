<?php

namespace App\Notifications;

use App\Enums\PaymentStatusEnum;
use App\Models\Payment;
use App\Models\TrainingRegistration;
use App\Notifications\Concerns\GeneratesPaymentQrCode;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\URL;

class TrainingRegistrationPaymentDue extends Notification implements ShouldQueue
{
    use GeneratesPaymentQrCode, Queueable;

    public function __construct(
        public TrainingRegistration $registration,
    ) {}

    /** @return list<string> */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $user = $notifiable;
        $training = $this->registration->training;
        $team = $training?->team;
        $teamName = $team?->getTranslation('name', 'sk') ?? '';
        $trainingTitle = $training?->getTranslation('title', 'sk') ?? 'Tréning';
        $feeAmount = number_format((float) ($training?->price_amount ?? 0), 2);
        $feeCurrency = 'EUR';
        $paymentDeadline = $this->registration->payment_due_at?->format('d.m.Y') ?? '';

        $payment = $this->findOrCreatePendingPayment($user);
        $paymentUrl = $payment
            ? URL::signedRoute('payment.page', ['payment' => $payment->id])
            : url('/admin');

        return (new MailMessage)
            ->subject('Platba za tréning — '.$trainingTitle)
            ->view('emails.training-payment-due', [
                'user' => $user,
                'registration' => $this->registration,
                'training' => $training,
                'teamName' => $teamName,
                'trainingTitle' => $trainingTitle,
                'feeAmount' => $feeAmount,
                'feeCurrency' => $feeCurrency,
                'paymentDeadline' => $paymentDeadline,
                'paymentUrl' => $paymentUrl,
                'qrCodeImage' => $this->qrCodeImageForPayment($payment),
                'emailSubject' => 'Platba za tréning',
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
            'training_registration_id' => $this->registration->id,
            'training_title' => $this->registration->training?->getTranslation('title', 'sk'),
            'fee_amount' => $this->registration->training?->price_amount,
            'payment_due_at' => $this->registration->payment_due_at?->toIso8601String(),
            'type' => 'training_registration_payment_due',
        ];
    }

    private function findOrCreatePendingPayment(object $user): ?Payment
    {
        $registration = $this->registration;
        $training = $registration->training;

        if (! $training?->team_id || (float) ($training->price_amount ?? 0) <= 0) {
            return null;
        }

        $existing = Payment::query()
            ->where('payable_type', $registration->getMorphClass())
            ->where('payable_id', $registration->id)
            ->whereIn('status', [PaymentStatusEnum::PENDING, PaymentStatusEnum::COMPLETED])
            ->first();

        if ($existing) {
            return $existing;
        }

        return Payment::create([
            'team_id' => $training->team_id,
            'user_id' => $user->id ?? null,
            'payer_name' => $user->name ?? null,
            'payer_email' => $user->email ?? null,
            'payable_type' => $registration->getMorphClass(),
            'payable_id' => $registration->getKey(),
            'amount' => $training->price_amount,
            'currency' => 'EUR',
            'status' => PaymentStatusEnum::PENDING,
        ]);
    }
}
