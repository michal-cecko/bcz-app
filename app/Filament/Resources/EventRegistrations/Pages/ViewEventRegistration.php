<?php

namespace App\Filament\Resources\EventRegistrations\Pages;

use App\Enums\EventPricingTypeEnum;
use App\Enums\PaymentMethodEnum;
use App\Enums\PaymentStatusEnum;
use App\Enums\RegistrationStatusEnum;
use App\Filament\Resources\EventRegistrations\EventRegistrationResource;
use App\Models\EventRegistration;
use App\Models\Payment;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Filament\Support\Icons\Heroicon;

class ViewEventRegistration extends ViewRecord
{
    protected static string $resource = EventRegistrationResource::class;

    protected function getHeaderActions(): array
    {
        /** @var EventRegistration $record */
        $record = $this->getRecord();
        $event = $record->event;
        $org = $event?->organization;

        return [
            Action::make('approve')
                ->label('Schváliť')
                ->icon(Heroicon::CheckCircle)
                ->color('success')
                ->visible(fn (): bool => in_array($record->status, [RegistrationStatusEnum::Pending, RegistrationStatusEnum::Rejected, RegistrationStatusEnum::Cancelled]))
                ->requiresConfirmation()
                ->action(function () use ($record): void {
                    $record->update(['status' => RegistrationStatusEnum::Approved]);
                    Notification::make()->success()->title('Registrácia bola schválená.')->send();
                    $this->refreshFormData(['status']);
                }),

            Action::make('reject')
                ->label('Zamietnuť')
                ->icon(Heroicon::XCircle)
                ->color('danger')
                ->visible(fn (): bool => in_array($record->status, [RegistrationStatusEnum::Pending, RegistrationStatusEnum::Approved]))
                ->schema([
                    Textarea::make('reason')
                        ->label('Dôvod zamietnutia')
                        ->rows(2),
                ])
                ->action(function () use ($record): void {
                    $record->update(['status' => RegistrationStatusEnum::Rejected]);
                    Notification::make()->success()->title('Registrácia bola zamietnutá.')->send();
                    $this->refreshFormData(['status']);
                }),

            Action::make('record_payment')
                ->label('Zaznamenať platbu')
                ->icon(Heroicon::CurrencyEuro)
                ->color('warning')
                ->visible(fn (): bool => $org?->pricing_type === EventPricingTypeEnum::Paid && $org->price_amount > 0)
                ->schema([
                    TextInput::make('amount')
                        ->label('Suma')
                        ->numeric()
                        ->required()
                        ->default($org?->price_amount)
                        ->prefix($org?->price_currency ?? '€'),
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
                ])
                ->action(function (array $data) use ($record, $event, $org): void {
                    $user = $record->user;
                    $paymentStatus = $data['payment_status'] instanceof PaymentStatusEnum
                        ? $data['payment_status']
                        : PaymentStatusEnum::from($data['payment_status']);

                    Payment::create([
                        'team_id' => $event->team_id,
                        'user_id' => $record->user_id,
                        'payer_name' => $user?->name,
                        'payer_email' => $user?->email,
                        'payable_type' => $record->getMorphClass(),
                        'payable_id' => $record->id,
                        'amount' => $data['amount'],
                        'currency' => $org?->price_currency ?? 'EUR',
                        'status' => $paymentStatus,
                        'payment_method' => $data['payment_method'],
                        'paid_at' => now(),
                        'notes' => $data['notes'] ?? null,
                    ]);

                    if ($paymentStatus === PaymentStatusEnum::COMPLETED) {
                        $record->update(['status' => RegistrationStatusEnum::Approved]);
                    }

                    Notification::make()->success()->title('Platba bola zaznamenaná.')->send();
                    $this->refreshFormData(['status']);
                }),
        ];
    }
}
