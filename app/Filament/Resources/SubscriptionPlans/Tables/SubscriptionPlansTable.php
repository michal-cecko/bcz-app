<?php

namespace App\Filament\Resources\SubscriptionPlans\Tables;

use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class SubscriptionPlansTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Názov')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('tier')
                    ->label('Úroveň')
                    ->badge()
                    ->sortable(),
                IconColumn::make('is_active')
                    ->label('Aktívny')
                    ->boolean()
                    ->sortable(),
                TextColumn::make('sort_order')
                    ->label('Poradie')
                    ->sortable(),
                TextColumn::make('subscriptions_count')
                    ->label('Predplatné')
                    ->counts('subscriptions')
                    ->sortable(),
            ])
            ->defaultSort('sort_order')
            ->recordActions([
                EditAction::make(),
            ]);
    }
}
