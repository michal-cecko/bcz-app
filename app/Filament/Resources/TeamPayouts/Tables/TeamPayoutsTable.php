<?php

namespace App\Filament\Resources\TeamPayouts\Tables;

use App\Enums\PayoutStatusEnum;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

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
            ]);
    }
}
