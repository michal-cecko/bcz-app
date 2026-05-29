<?php

namespace App\Filament\Resources\TrainingRegistrations\Pages;

use App\Enums\PaymentMethodEnum;
use App\Enums\PaymentStatusEnum;
use App\Enums\RegistrationStatusEnum;
use App\Enums\TrainingPricingTypeEnum;
use App\Filament\Resources\TrainingRegistrations\TrainingRegistrationResource;
use App\Models\Payment;
use App\Models\TrainingRegistration;
use App\Notifications\PaymentConfirmed;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Filament\Support\Icons\Heroicon;

class ViewTrainingRegistration extends ViewRecord
{
    protected static string $resource = TrainingRegistrationResource::class;

    protected function getHeaderActions(): array
    {
        /** @var TrainingRegistration $record */
        $record = $this->getRecord();
        $training = $record->training;

        return [
            Action::make('approve')
                ->label('Schváliť')
                ->icon(Heroicon::CheckCircle)
                ->color('success')
                ->visible(fn (): bool => in_array($record->status, [RegistrationStatusEnum::Pending, RegistrationStatusEnum::Rejected, RegistrationStatusEnum::Cancelled]))
                ->requiresConfirmation()
                ->action(function () use ($record): void {
                    $record->update(['status' => RegistrationStatusEnum::Approved, 'payment_due_at' => null]);
                    Notification::make()->success()->title('Registrácia bola schválená.')->send();
                    $this->refreshFormData(['status']);
                }),

            Action::make('reject')
                ->label('Zamietnuť')
                ->icon(Heroicon::XCircle)
                ->color('danger')
                ->visible(fn (): bool => in_array($record->status, [RegistrationStatusEnum::Pending, RegistrationStatusEnum::Approved]))
                ->schema([
                    Textarea::make('cancellation_reason')
                        ->label('Dôvod zamietnutia')
                        ->rows(2),
                ])
                ->action(function (array $data) use ($record): void {
                    $record->update([
                        'status' => RegistrationStatusEnum::Rejected,
                        'cancellation_reason' => $data['cancellation_reason'] ?? null,
                    ]);
                    Notification::make()->success()->title('Registrácia bola zamietnutá.')->send();
                    $this->refreshFormData(['status']);
                }),

            Action::make('cancel')
                ->label('Zrušiť registráciu')
                ->icon(Heroicon::NoSymbol)
                ->color('gray')
                ->visible(fn (): bool => $record->status !== RegistrationStatusEnum::Cancelled)
                ->schema([
                    Textarea::make('cancellation_reason')
                        ->label('Dôvod zrušenia')
                        ->rows(2),
                ])
                ->modalHeading('Zrušiť registráciu?')
                ->modalDescription('Registrácia bude označená ako zrušená a všetky čakajúce platby budú zrušené.')
                ->modalSubmitActionLabel('Áno, zrušiť')
                ->modalCancelActionLabel('Späť')
                ->action(function (array $data) use ($record): void {
                    $record->update([
                        'status' => RegistrationStatusEnum::Cancelled,
                        'cancellation_reason' => $data['cancellation_reason'] ?? null,
                    ]);
                    $record->payments()
                        ->where('status', PaymentStatusEnum::PENDING)
                        ->update(['status' => PaymentStatusEnum::CANCELLED->value]);
                    Notification::make()->success()->title('Registrácia bola zrušená.')->send();
                    $this->refreshFormData(['status']);
                }),

            Action::make('record_payment')
                ->label('Zaznamenať platbu')
                ->icon(Heroicon::CurrencyEuro)
                ->color('warning')
                ->visible(fn (): bool => $training?->pricing_type === TrainingPricingTypeEnum::PAID)
                ->schema([
                    TextInput::make('amount')
                        ->label('Suma')
                        ->numeric()
                        ->required()
                        ->default($training?->price_amount)
                        ->prefix('€'),
                    Select::make('payment_method')
                        ->label('Metóda platby')
                        ->options([
                            PaymentMethodEnum::BANK_TRANSFER->value => PaymentMethodEnum::BANK_TRANSFER->getLabel(),
                            PaymentMethodEnum::CASH->value => PaymentMethodEnum::CASH->getLabel(),
                        ])
                        ->required()
                        ->default(PaymentMethodEnum::CASH),
                    Select::make('payment_status')
                        ->label('Stav platby')
                        ->options(PaymentStatusEnum::class)
                        ->required()
                        ->default(PaymentStatusEnum::COMPLETED),
                    Textarea::make('notes')
                        ->label('Poznámka')
                        ->rows(2),
                    Toggle::make('notify_customer')
                        ->label('Odoslať potvrdenie zákazníkovi')
                        ->helperText('Pošle e-mail s potvrdením platby.')
                        ->default(true),
                ])
                ->action(function (array $data) use ($record, $training): void {
                    $user = $record->user;
                    $paymentStatus = $data['payment_status'] instanceof PaymentStatusEnum
                        ? $data['payment_status']
                        : PaymentStatusEnum::from($data['payment_status']);

                    $payment = Payment::create([
                        'team_id' => $training->team_id,
                        'user_id' => $record->user_id,
                        'payer_name' => $user?->name,
                        'payer_email' => $user?->email,
                        'payable_type' => $record->getMorphClass(),
                        'payable_id' => $record->id,
                        'amount' => $data['amount'],
                        'currency' => 'EUR',
                        'status' => $paymentStatus,
                        'payment_method' => $data['payment_method'],
                        'paid_at' => now(),
                        'notes' => $data['notes'] ?? null,
                    ]);

                    if ($paymentStatus === PaymentStatusEnum::COMPLETED) {
                        $record->update(['status' => RegistrationStatusEnum::Approved, 'payment_due_at' => null]);
                    }

                    if (! empty($data['notify_customer']) && $paymentStatus === PaymentStatusEnum::COMPLETED && $user) {
                        $user->notify(new PaymentConfirmed($payment));
                    }

                    Notification::make()->success()->title('Platba bola zaznamenaná.')->send();
                    $this->refreshFormData(['status']);
                }),
        ];
    }
}
