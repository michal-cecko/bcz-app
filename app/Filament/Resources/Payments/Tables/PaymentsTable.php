<?php

namespace App\Filament\Resources\Payments\Tables;

use App\Enums\PaymentMethodEnum;
use App\Enums\PaymentStatusEnum;
use App\Filament\Resources\Payments\PaymentResource;
use App\Models\Payment;
use App\Services\PaymentService;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class PaymentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('display_name')
                    ->label('Používateľ')
                    ->searchable(query: function ($query, string $search): void {
                        $query->where(function ($q) use ($search): void {
                            $q->whereHas('user', fn ($q) => $q->where('name', 'ilike', "%{$search}%"))
                                ->orWhere('payer_name', 'ilike', "%{$search}%");
                        });
                    }),
                TextColumn::make('payable_type')
                    ->label('Typ')
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'membership' => 'Členstvo',
                        'training_registration' => 'Tréning',
                        'competition_registration' => 'Súťaž',
                        'team_subscription' => 'Predplatné',
                        default => $state,
                    })
                    ->badge(),
                TextColumn::make('amount')
                    ->label('Suma')
                    ->formatStateUsing(fn ($record): string => number_format((float) $record->amount, 2).' '.$record->currency)
                    ->sortable(),
                TextColumn::make('status')
                    ->label('Stav')
                    ->badge()
                    ->sortable(),
                TextColumn::make('payment_method')
                    ->label('Spôsob')
                    ->badge()
                    ->sortable(),
                TextColumn::make('paid_at')
                    ->label('Zaplatené')
                    ->dateTime()
                    ->placeholder('-')
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('Vytvorené')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->recordUrl(fn (Payment $record): string => PaymentResource::getUrl('view', ['record' => $record]))
            ->filters([
                SelectFilter::make('status')
                    ->label('Stav')
                    ->options(PaymentStatusEnum::translations()),
                SelectFilter::make('payment_method')
                    ->label('Spôsob platby')
                    ->options(PaymentMethodEnum::translations()),
            ])
            ->recordActions([
                EditAction::make()
                    ->visible(fn (): bool => ! auth()->user()?->isMemberLevel())
                    ->schema([
                        TextInput::make('amount')
                            ->label('Suma')
                            ->numeric()
                            ->required()
                            ->minValue(0.01),
                        Select::make('currency')
                            ->label('Mena')
                            ->options([
                                'EUR' => 'EUR',
                                'CZK' => 'CZK',
                                'USD' => 'USD',
                            ])
                            ->required(),
                        Select::make('payment_method')
                            ->label('Spôsob platby')
                            ->options(PaymentMethodEnum::translations())
                            ->required(),
                        Select::make('status')
                            ->label('Stav')
                            ->options(PaymentStatusEnum::translations())
                            ->required(),
                        DateTimePicker::make('paid_at')
                            ->label('Zaplatené'),
                        TextInput::make('variable_symbol')
                            ->label('Variabilný symbol'),
                        Textarea::make('notes')
                            ->label('Poznámky')
                            ->rows(2),
                        Toggle::make('notify_customer')
                            ->label('Upozorniť zákazníka?')
                            ->helperText('Pošle e-mail s potvrdením platby.')
                            ->default(false),
                    ])
                    ->after(function (Payment $record, array $data): void {
                        if ($record->status === PaymentStatusEnum::COMPLETED) {
                            $record->load('payable');
                            app(PaymentService::class)->processPaymentCompleted(
                                $record,
                                ! empty($data['notify_customer']),
                            );
                        }
                    }),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
