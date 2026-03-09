<?php

namespace App\Filament\Resources\Menus\Tables;

use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class MenusTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('location')
                    ->label('Umiestnenie')
                    ->badge(),
                TextColumn::make('label')
                    ->label('Názov')
                    ->formatStateUsing(fn ($record): string => $record->getTranslation('label', 'sk')),
                TextColumn::make('items_count')
                    ->label('Položky')
                    ->state(fn ($record): int => count($record->items ?? [])),
                TextColumn::make('updated_at')
                    ->label('Aktualizované')
                    ->since()
                    ->sortable(),
            ])
            ->recordActions([
                EditAction::make(),
            ]);
    }
}
