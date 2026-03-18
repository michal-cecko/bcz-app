<?php

namespace App\Filament\Resources\Banners\Tables;

use App\Enums\BannerTypeEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class BannersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('sort_order')
            ->columns([
                TextColumn::make('name')
                    ->label('Názov')
                    ->searchable()
                    ->sortable()
                    ->formatStateUsing(fn ($record): string => $record->getTranslation('name', 'sk')),
                TextColumn::make('type')
                    ->label('Typ')
                    ->badge()
                    ->sortable(),
                TextColumn::make('placement')
                    ->label('Umiestnenie')
                    ->formatStateUsing(fn (string $state): string => $state === 'all' ? 'Všetky stránky' : 'Konkrétna stránka')
                    ->sortable(),
                TextColumn::make('page.title')
                    ->label('Stránka')
                    ->formatStateUsing(fn ($record): string => $record->page?->getTranslation('title', 'sk') ?? '-')
                    ->toggleable(),
                IconColumn::make('is_active')
                    ->label('Aktívny')
                    ->boolean()
                    ->sortable(),
                TextColumn::make('active_from')
                    ->label('Od')
                    ->dateTime('d.m.Y H:i')
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('active_to')
                    ->label('Do')
                    ->dateTime('d.m.Y H:i')
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([
                SelectFilter::make('type')
                    ->options(BannerTypeEnum::class)
                    ->label('Typ'),
                TernaryFilter::make('is_active')
                    ->label('Aktívny'),
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
