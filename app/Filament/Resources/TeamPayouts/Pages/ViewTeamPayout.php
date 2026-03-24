<?php

namespace App\Filament\Resources\TeamPayouts\Pages;

use App\Enums\PayoutStatusEnum;
use App\Filament\Resources\TeamPayouts\TeamPayoutResource;
use App\Services\QrPaymentService;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Support\HtmlString;

class ViewTeamPayout extends ViewRecord
{
    protected static string $resource = TeamPayoutResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('payoutQrCode')
                ->label('QR kód na úhradu')
                ->icon('heroicon-o-qr-code')
                ->color('info')
                ->visible(fn (): bool => $this->record->status === PayoutStatusEnum::PENDING
                    && $this->record->bank_account_iban
                    && ! auth()->user()?->isMemberLevel())
                ->modalContent(function (): HtmlString {
                    $payout = $this->record;
                    $isCzk = strtoupper($payout->currency) === 'CZK';

                    $html = '<div class="space-y-4">';
                    $html .= '<p class="text-sm text-gray-500">Naskenujte QR kód na úhradu výplaty pre tím.</p>';
                    $html .= '<div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-3 text-sm space-y-1">';
                    $html .= '<div><span class="font-medium">IBAN:</span> '.$payout->bank_account_iban.'</div>';
                    $html .= '<div><span class="font-medium">Príjemca:</span> '.($payout->bank_account_name ?: '-').'</div>';
                    $html .= '<div><span class="font-medium">Suma:</span> '.number_format((float) $payout->net_amount, 2).' '.$payout->currency.'</div>';
                    if ($payout->reference) {
                        $html .= '<div><span class="font-medium">Referencia:</span> '.$payout->reference.'</div>';
                    }
                    $html .= '</div>';

                    if ($isCzk) {
                        $qr = QrPaymentService::qrPlatba(
                            iban: $payout->bank_account_iban,
                            amount: (float) $payout->net_amount,
                            currency: 'CZK',
                            variableSymbol: $payout->reference ?? '',
                            recipientName: $payout->bank_account_name ?? '',
                        );
                        $label = 'QR Platba (CZ)';
                    } else {
                        $qr = QrPaymentService::payBySquare(
                            iban: $payout->bank_account_iban,
                            amount: (float) $payout->net_amount,
                            currency: $payout->currency,
                            variableSymbol: $payout->reference ?? '',
                            recipientName: $payout->bank_account_name ?? '',
                        );
                        $label = 'Pay by Square';
                    }

                    if ($qr) {
                        $html .= '<div><h3 class="font-semibold mb-2">'.$label.'</h3><img src="data:image/png;base64,'.$qr.'" alt="'.$label.'" class="w-48"></div>';
                    } else {
                        $html .= '<p class="text-gray-500">IBAN nie je nastavený.</p>';
                    }

                    $html .= '</div>';

                    return new HtmlString($html);
                })
                ->modalSubmitAction(false),

            Action::make('markAsCompleted')
                ->label('Označiť ako uhradené')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->visible(fn (): bool => $this->record->status === PayoutStatusEnum::PENDING
                    && ! auth()->user()?->isMemberLevel())
                ->requiresConfirmation()
                ->modalHeading('Potvrdiť úhradu výplaty')
                ->modalDescription('Naozaj chcete označiť túto výplatu ako uhradenú? Táto akcia sa nedá vrátiť.')
                ->action(function (): void {
                    $this->record->update([
                        'status' => PayoutStatusEnum::COMPLETED,
                        'paid_at' => now(),
                    ]);

                    Notification::make()
                        ->title('Výplata bola označená ako uhradená.')
                        ->success()
                        ->send();
                }),
        ];
    }
}
