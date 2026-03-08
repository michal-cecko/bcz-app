<?php

namespace App\Filament\Resources\Payments\Tables;

use App\Enums\PaymentMethodEnum;
use App\Enums\PaymentStatusEnum;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class PaymentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')
                    ->label('Používateľ')
                    ->searchable()
                    ->sortable(),
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
            ->filters([
                SelectFilter::make('status')
                    ->label('Stav')
                    ->options(PaymentStatusEnum::translations()),
                SelectFilter::make('payment_method')
                    ->label('Spôsob platby')
                    ->options(PaymentMethodEnum::translations()),
            ])
            ->recordActions([
                ViewAction::make(),
            ]);
    }
}
