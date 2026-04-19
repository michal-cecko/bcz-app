<?php

namespace App\Filament\Resources\Events\Tables;

use App\Enums\EventTypeEnum;
use App\Filament\Resources\Events\EventResource;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class EventsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('date', 'desc')
            ->columns([
                SpatieMediaLibraryImageColumn::make('card_image')
                    ->collection('card_image')
                    ->label('Obrázok')
                    ->circular()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('event_type')
                    ->label('Typ')
                    ->badge()
                    ->sortable(),
                TextColumn::make('title')
                    ->label('Názov')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('eventCategory.title')
                    ->label('Kategória')
                    ->state(fn ($record): ?string => $record->eventCategory?->getTranslation('title', 'sk'))
                    ->badge(),
                TextColumn::make('date')
                    ->label('Dátum')
                    ->date()
                    ->sortable(),
                TextColumn::make('city')
                    ->label('Mesto')
                    ->searchable(),
                TextColumn::make('attendee_count')
                    ->label('Účastníci')
                    ->placeholder('-'),
                IconColumn::make('is_published')
                    ->label('Publikované')
                    ->boolean()
                    ->sortable(),
                TextColumn::make('published_at')
                    ->label('Publikované dňa')
                    ->dateTime()
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
                SelectFilter::make('event_type')
                    ->label('Typ')
                    ->options(EventTypeEnum::class),
                SelectFilter::make('event_category_id')
                    ->relationship('eventCategory', 'id')
                    ->getOptionLabelFromRecordUsing(fn (Model $record): string => $record->getTranslation('title', 'sk'))
                    ->label('Kategória')
                    ->preload(),
                TernaryFilter::make('is_published')
                    ->label('Publikované'),
            ])
            ->recordUrl(fn (Model $record): string => EventResource::getUrl('view', ['record' => $record]))
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
