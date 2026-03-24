<?php

namespace App\Filament\Resources\TeamPayouts\Tables;

use App\Enums\PayoutStatusEnum;
use App\Models\TeamPayout;
use App\Services\QrPaymentService;
use Filament\Actions\Action;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\HtmlString;

class TeamPayoutsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('net_amount')
                    ->label('Čistá suma')
                    ->formatStateUsing(fn ($record): string => number_format((float) $record->net_amount, 2).' '.$record->currency)
                    ->sortable(),
                TextColumn::make('gross_amount')
                    ->label('Hrubá suma')
                    ->formatStateUsing(fn ($record): string => number_format((float) $record->gross_amount, 2).' '.$record->currency)
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('fee_amount')
                    ->label('Poplatok')
                    ->formatStateUsing(fn ($record): string => number_format((float) $record->fee_amount, 2).' '.$record->currency)
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('status')
                    ->label('Stav')
                    ->badge()
                    ->sortable(),
                TextColumn::make('period_from')
                    ->label('Obdobie od')
                    ->date()
                    ->sortable(),
                TextColumn::make('period_to')
                    ->label('Obdobie do')
                    ->date()
                    ->sortable(),
                TextColumn::make('paid_at')
                    ->label('Vyplatené')
                    ->dateTime()
                    ->placeholder('-')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('status')
                    ->label('Stav')
                    ->options(PayoutStatusEnum::translations()),
            ])
            ->recordActions([
                ViewAction::make(),
                Action::make('qrCode')
                    ->label('QR')
                    ->icon('heroicon-o-qr-code')
                    ->visible(fn (TeamPayout $record): bool => $record->status === PayoutStatusEnum::PENDING
                        && $record->bank_account_iban
                        && ! auth()->user()?->isMemberLevel())
                    ->modalContent(function (TeamPayout $record): HtmlString {
                        $isCzk = strtoupper($record->currency) === 'CZK';

                        $html = '<div class="space-y-4">';
                        $html .= '<div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-3 text-sm space-y-1">';
                        $html .= '<div><span class="font-medium">IBAN:</span> '.$record->bank_account_iban.'</div>';
                        $html .= '<div><span class="font-medium">Suma:</span> '.number_format((float) $record->net_amount, 2).' '.$record->currency.'</div>';
                        $html .= '</div>';

                        if ($isCzk) {
                            $qr = QrPaymentService::qrPlatba(
                                iban: $record->bank_account_iban,
                                amount: (float) $record->net_amount,
                                currency: 'CZK',
                                variableSymbol: $record->reference ?? '',
                                recipientName: $record->bank_account_name ?? '',
                            );
                            $label = 'QR Platba (CZ)';
                        } else {
                            $qr = QrPaymentService::payBySquare(
                                iban: $record->bank_account_iban,
                                amount: (float) $record->net_amount,
                                currency: $record->currency,
                                variableSymbol: $record->reference ?? '',
                                recipientName: $record->bank_account_name ?? '',
                            );
                            $label = 'Pay by Square';
                        }

                        if ($qr) {
                            $html .= '<div><h3 class="font-semibold mb-2">'.$label.'</h3><img src="data:image/png;base64,'.$qr.'" alt="'.$label.'" class="w-48"></div>';
                        }

                        $html .= '</div>';

                        return new HtmlString($html);
                    })
                    ->modalSubmitAction(false),
                Action::make('markPaid')
                    ->label('Uhradené')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn (TeamPayout $record): bool => $record->status === PayoutStatusEnum::PENDING
                        && ! auth()->user()?->isMemberLevel())
                    ->requiresConfirmation()
                    ->action(function (TeamPayout $record): void {
                        $record->update([
                            'status' => PayoutStatusEnum::COMPLETED,
                            'paid_at' => now(),
                        ]);

                        Notification::make()
                            ->title('Výplata bola označená ako uhradená.')
                            ->success()
                            ->send();
                    }),
            ]);
    }
}
