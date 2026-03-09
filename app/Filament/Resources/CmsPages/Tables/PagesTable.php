<?php

namespace App\Filament\Resources\CmsPages\Tables;

use App\Enums\PageStatusEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class PagesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('sort_order')
            ->columns([
                TextColumn::make('title')
                    ->label('Názov')
                    ->searchable()
                    ->sortable()
                    ->formatStateUsing(fn ($record): string => $record->getTranslation('title', 'sk')),
                TextColumn::make('slug')
                    ->label('Slug')
                    ->searchable(),
                TextColumn::make('status')
                    ->label('Stav')
                    ->badge()
                    ->sortable(),
                IconColumn::make('is_system')
                    ->label('Systémová')
                    ->boolean()
                    ->sortable(),
                TextColumn::make('published_at')
                    ->label('Publikované')
                    ->since()
                    ->sortable(),
                TextColumn::make('updated_at')
                    ->label('Aktualizované')
                    ->since()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options(PageStatusEnum::class)
                    ->label('Stav'),
                TernaryFilter::make('is_system')
                    ->label('Systémová'),
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
