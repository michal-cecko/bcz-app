<?php

namespace App\Filament\Resources\Sponsors\Tables;

use App\Enums\SponsorTagEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use RalphJSmit\Filament\MediaLibrary\Filament\Tables\Columns\MediaColumn;

class SponsorsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->columns([
                MediaColumn::make('logo')
                    ->label('Logo')
                    ->size(40),
                TextColumn::make('name')
                    ->label('Názov')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('tag')
                    ->label('Typ')
                    ->badge(),
                TextColumn::make('link')
                    ->label('Odkaz')
                    ->limit(30)
                    ->url(fn ($record) => $record->link, shouldOpenInNewTab: true),
                IconColumn::make('is_visible')
                    ->label('Viditeľný')
                    ->boolean()
                    ->sortable(),
                TextColumn::make('sort_order')
                    ->label('Poradie')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
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
            ->filters([
                SelectFilter::make('tag')
                    ->options(SponsorTagEnum::class),
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
