<?php

namespace App\Filament\Resources\Memberships\Tables;

use App\Enums\MembershipStatusEnum;
use App\Enums\PaymentMethodEnum;
use App\Enums\PaymentStatusEnum;
use App\Filament\Actions\SendEmailAction;
use App\Filament\Actions\SendEmailBulkAction;
use App\Models\Membership;
use App\Services\PaymentService;
use App\Services\QrPaymentService;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Facades\Filament;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\HtmlString;

class MembershipsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')
                    ->label('Používateľ')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('season.name')
                    ->label('Sezóna')
                    ->placeholder('-')
                    ->sortable(),
                TextColumn::make('status')
                    ->label('Stav')
                    ->badge()
                    ->sortable(),
                IconColumn::make('is_free')
                    ->label('Zadarmo')
                    ->boolean(),
                TextColumn::make('fee_amount')
                    ->label('Suma')
                    ->formatStateUsing(fn ($record): string => number_format((float) $record->fee_amount, 2).' '.$record->fee_currency)
                    ->sortable(),
                TextColumn::make('starts_at')
                    ->label('Začiatok')
                    ->date()
                    ->sortable(),
                TextColumn::make('ends_at')
                    ->label('Koniec')
                    ->date()
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('status')
                    ->label('Stav')
                    ->options(MembershipStatusEnum::translations()),
                SelectFilter::make('team_season_id')
                    ->label('Sezóna')
                    ->relationship('season', 'name'),
            ])
            ->recordActions([
                SendEmailAction::make('send_email')
                    ->visible(fn (): bool => ! auth()->user()?->isMemberLevel())
                    ->resolveRecipients(function (Membership $record) {
                        if (! $record->user?->email) {
                            return [];
                        }

                        $team = Filament::getTenant();

                        return [
                            [
                                'email' => $record->user->email,
                                'variables' => [
                                    'meno' => $record->user->name,
                                    'email' => $record->user->email,
                                    'nazov_timu' => $team?->getTranslation('name', 'sk') ?? '',
                                ],
                            ],
                        ];
                    }),
                EditAction::make()
                    ->visible(fn (): bool => ! auth()->user()?->isMemberLevel()),
                Action::make('recordPayment')
                    ->label('Zaznamenať platbu')
                    ->icon('heroicon-o-banknotes')
                    ->visible(fn (): bool => ! auth()->user()?->isMemberLevel())
                    ->schema([
                        TextInput::make('amount')
                            ->label('Suma')
                            ->numeric()
                            ->required()
                            ->default(fn (Membership $record): string => (string) $record->fee_amount),
                        Select::make('currency')
                            ->label('Mena')
                            ->options(['EUR' => 'EUR', 'CZK' => 'CZK', 'USD' => 'USD'])
                            ->default(fn (Membership $record): string => $record->fee_currency)
                            ->required(),
                        Select::make('payment_method')
                            ->label('Spôsob platby')
                            ->options(PaymentMethodEnum::translations())
                            ->default(PaymentMethodEnum::CASH->value)
                            ->required(),
                        Textarea::make('notes')
                            ->label('Poznámky')
                            ->rows(2),
                        Toggle::make('notify_customer')
                            ->label('Upozorniť zákazníka?')
                            ->helperText('Pošle e-mail s potvrdením platby.')
                            ->default(true),
                    ])
                    ->action(function (array $data, Membership $record): void {
                        $paymentService = app(PaymentService::class);
                        $paymentService->recordManualPayment(
                            $record->user,
                            Filament::getTenant(),
                            $record,
                            (float) $data['amount'],
                            $data['currency'],
                            PaymentMethodEnum::from($data['payment_method']),
                            $data['notes'] ?? null,
                            ! empty($data['notify_customer']),
                        );

                        Notification::make()
                            ->title('Platba bola zaznamenaná.')
                            ->success()
                            ->send();
                    }),
                Action::make('cancel')
                    ->label('Zrušiť členstvo')
                    ->icon('heroicon-o-no-symbol')
                    ->color('gray')
                    ->visible(fn (Membership $record): bool => ! auth()->user()?->isMemberLevel()
                        && $record->status !== MembershipStatusEnum::CANCELLED)
                    ->requiresConfirmation()
                    ->modalHeading('Zrušiť členstvo?')
                    ->modalDescription('Členstvo bude označené ako zrušené a všetky čakajúce platby budú zrušené.')
                    ->modalSubmitActionLabel('Áno, zrušiť')
                    ->modalCancelActionLabel('Späť')
                    ->action(function (Membership $record): void {
                        $record->update(['status' => MembershipStatusEnum::CANCELLED]);
                        $record->payments()
                            ->where('status', PaymentStatusEnum::PENDING)
                            ->update(['status' => PaymentStatusEnum::CANCELLED->value]);

                        Notification::make()
                            ->title('Členstvo bolo zrušené.')
                            ->success()
                            ->send();
                    }),
                Action::make('qrCode')
                    ->label('QR kód')
                    ->icon('heroicon-o-qr-code')
                    ->visible(fn (): bool => ! auth()->user()?->isMemberLevel())
                    ->modalContent(function (Membership $record): HtmlString {
                        $latestPayment = $record->payments()->latest()->first();

                        if (! $latestPayment) {
                            return new HtmlString('<p class="text-gray-500">Žiadna platba na generovanie QR kódu.</p>');
                        }

                        $qr = app(QrPaymentService::class)->generateQrForPayment($latestPayment);

                        if (! $qr) {
                            return new HtmlString('<p class="text-gray-500">IBAN nie je nastavený v nastaveniach tímu.</p>');
                        }

                        return new HtmlString('<div class="flex justify-center"><img src="data:image/png;base64,'.$qr.'" alt="QR Platba" class="w-64"></div>');
                    })
                    ->modalSubmitAction(false),
            ])
            ->toolbarActions([
                SendEmailBulkAction::make('send_email_bulk')
                    ->visible(fn (): bool => ! auth()->user()?->isMemberLevel())
                    ->resolveRecipients(function (Membership $record) {
                        if (! $record->user?->email) {
                            return [];
                        }

                        $team = Filament::getTenant();

                        return [
                            [
                                'email' => $record->user->email,
                                'variables' => [
                                    'meno' => $record->user->name,
                                    'email' => $record->user->email,
                                    'nazov_timu' => $team?->getTranslation('name', 'sk') ?? '',
                                ],
                            ],
                        ];
                    }),
            ]);
    }
}
