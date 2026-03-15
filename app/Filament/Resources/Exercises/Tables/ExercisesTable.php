<?php

namespace App\Filament\Resources\Exercises\Tables;

use App\Enums\ComplexityLevelEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class ExercisesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                SpatieMediaLibraryImageColumn::make('image')
                    ->collection('image')
                    ->label('Obrázok')
                    ->circular(),
                TextColumn::make('name')
                    ->label('Názov')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('exerciseCategory.name')
                    ->label('Kategória')
                    ->state(fn ($record): ?string => $record->exerciseCategory?->getTranslation('name', 'sk'))
                    ->sortable(),
                TextColumn::make('complexity')
                    ->label('Obtiažnosť')
                    ->badge()
                    ->color(fn (ComplexityLevelEnum $state): string => match ($state) {
                        ComplexityLevelEnum::BASIC => 'success',
                        ComplexityLevelEnum::INTERMEDIATE => 'info',
                        ComplexityLevelEnum::ADVANCED => 'warning',
                        ComplexityLevelEnum::ELITE => 'danger',
                    }),
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
                SelectFilter::make('complexity')
                    ->label('Obtiažnosť')
                    ->options(ComplexityLevelEnum::class),
                SelectFilter::make('exercise_category_id')
                    ->relationship('exerciseCategory', 'id')
                    ->getOptionLabelFromRecordUsing(fn (Model $record): string => $record->getTranslation('name', 'sk'))
                    ->label('Kategória')
                    ->preload(),
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
