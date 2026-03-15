<?php

namespace App\Filament\Resources\Payments;

use App\Filament\Clusters\Finances\FinancesCluster;
use App\Filament\Resources\Payments\Pages\ListPayments;
use App\Filament\Resources\Payments\Pages\ViewPayment;
use App\Filament\Resources\Payments\Tables\PaymentsTable;
use App\Models\Payment;
use BackedEnum;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Support\HtmlString;

class PaymentResource extends Resource
{
    protected static ?string $model = Payment::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBanknotes;

    protected static ?string $modelLabel = 'platbu';

    protected static ?string $pluralModelLabel = 'Platby';

    protected static bool $hasTitleCaseModelLabel = false;

    protected static ?string $cluster = FinancesCluster::class;

    protected static ?int $navigationSort = 1;

    protected static ?string $tenantOwnershipRelationshipName = 'team';

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make(3)
                    ->columnSpanFull()
                    ->schema([
                        Section::make('Detaily platby')
                            ->schema([
                                TextEntry::make('user.name')
                                    ->label('Používateľ'),
                                TextEntry::make('payable_type')
                                    ->label('Typ')
                                    ->formatStateUsing(fn (string $state): string => match ($state) {
                                        'membership' => 'Členstvo',
                                        'training_registration' => 'Registrácia na tréning',
                                        'competition_registration' => 'Registrácia na súťaž',
                                        'team_subscription' => 'Predplatné tímu',
                                        default => $state,
                                    }),
                                TextEntry::make('amount')
                                    ->label('Suma')
                                    ->formatStateUsing(fn ($record): string => number_format((float) $record->amount, 2).' '.$record->currency),
                                TextEntry::make('status')
                                    ->label('Stav')
                                    ->badge(),
                                TextEntry::make('payment_method')
                                    ->label('Spôsob platby')
                                    ->badge(),
                                TextEntry::make('variable_symbol')
                                    ->label('Variabilný symbol')
                                    ->placeholder('-'),
                                TextEntry::make('notes')
                                    ->label('Poznámky')
                                    ->placeholder('-')
                                    ->columnSpanFull(),
                            ])
                            ->columns(2)
                            ->columnSpan(2),

                        Grid::make(1)
                            ->schema([
                                Section::make('Dátumy')
                                    ->schema([
                                        TextEntry::make('paid_at')
                                            ->label('Zaplatené')
                                            ->dateTime()
                                            ->placeholder('-'),
                                        TextEntry::make('refunded_at')
                                            ->label('Vrátené')
                                            ->dateTime()
                                            ->placeholder('-'),
                                        TextEntry::make('created_at')
                                            ->label('Vytvorené')
                                            ->dateTime(),
                                    ]),

                                Section::make('Stripe')
                                    ->schema([
                                        TextEntry::make('stripe_payment_id')
                                            ->label('Payment ID')
                                            ->placeholder('-'),
                                        TextEntry::make('stripe_checkout_session_id')
                                            ->label('Checkout Session')
                                            ->placeholder('-'),
                                    ])
                                    ->visible(fn ($record): bool => $record->stripe_payment_id || $record->stripe_checkout_session_id),

                                Section::make('QR kód')
                                    ->schema([
                                        TextEntry::make('qr_pay_by_square')
                                            ->label('Pay by Square (SK)')
                                            ->state(function ($record): ?HtmlString {
                                                $qrService = app(\App\Services\QrPaymentService::class);
                                                $qr = $qrService->generatePayBySquareForPayment($record);

                                                return $qr ? new HtmlString('<img src="data:image/png;base64,'.$qr.'" alt="Pay by Square" class="w-48">') : null;
                                            })
                                            ->placeholder('IBAN nie je nastavený'),
                                        TextEntry::make('qr_platba')
                                            ->label('QR Platba (CZ)')
                                            ->state(function ($record): ?HtmlString {
                                                $qrService = app(\App\Services\QrPaymentService::class);
                                                $qr = $qrService->generateQrPlatbaForPayment($record);

                                                return $qr ? new HtmlString('<img src="data:image/png;base64,'.$qr.'" alt="QR Platba" class="w-48">') : null;
                                            })
                                            ->placeholder('IBAN nie je nastavený'),
                                    ])
                                    ->visible(fn ($record): bool => $record->payment_method === \App\Enums\PaymentMethodEnum::BANK_TRANSFER),
                            ])
                            ->columnSpan(1),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return PaymentsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPayments::route('/'),
            'view' => ViewPayment::route('/{record}'),
        ];
    }
}
