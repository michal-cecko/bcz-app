<?php

namespace App\Filament\Resources\PaymentMethods\Tables;

use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PaymentMethodsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->reorderable('sort_order')
            ->defaultSort('sort_order')
            ->columns([
                TextColumn::make('method')
                    ->label('Metóda')
                    ->badge(),
                TextColumn::make('title')
                    ->label('Názov')
                    ->searchable(['title->sk', 'title->en', 'title->cs']),
                TextColumn::make('description')
                    ->label('Popis')
                    ->html()
                    ->limit(80),
                IconColumn::make('is_active')
                    ->label('Aktívna')
                    ->boolean(),
                TextColumn::make('sort_order')
                    ->label('Poradie')
                    ->sortable(),
            ])
            ->recordActions([
                EditAction::make(),
            ]);
    }
}
