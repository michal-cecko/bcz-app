<?php

namespace App\Filament\Resources\AthleteCategories\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class AthleteCategoriesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->reorderable('sort_order')
            ->defaultSort('sort_order')
            ->columns([
                TextColumn::make('name')
                    ->label('Názov')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('parent.name')
                    ->label('Nadradená')
                    ->placeholder('-'),
                TextColumn::make('gender')
                    ->label('Pohlavie')
                    ->badge()
                    ->placeholder('Všetky'),
                TextColumn::make('min_weight')
                    ->label('Min. váha')
                    ->suffix(' kg')
                    ->placeholder('-'),
                TextColumn::make('max_weight')
                    ->label('Max. váha')
                    ->suffix(' kg')
                    ->placeholder('-'),
                TextColumn::make('min_age')
                    ->label('Min. vek')
                    ->placeholder('-'),
                TextColumn::make('max_age')
                    ->label('Max. vek')
                    ->placeholder('-'),
                TextColumn::make('competitions_count')
                    ->counts('competitions')
                    ->label('Súťaže')
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('Vytvorené')
                    ->since()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label('Aktualizované')
                    ->since()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
