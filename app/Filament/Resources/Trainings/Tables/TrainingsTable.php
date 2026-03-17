<?php

namespace App\Filament\Resources\Trainings\Tables;

use App\Enums\TrainingPricingTypeEnum;
use App\Filament\Resources\Trainings\TrainingResource;
use App\Models\Training;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class TrainingsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->reorderable('sort_order')
            ->defaultSort('sort_order')
            ->columns([
                TextColumn::make('title')
                    ->label('Názov')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('sportCategory.name')
                    ->label('Šport')
                    ->state(fn (Training $record): ?string => $record->sportCategory?->getTranslation('name', 'sk')),
                TextColumn::make('min_age')
                    ->label('Vek')
                    ->formatStateUsing(function (Training $record): string {
                        if ($record->min_age === null && $record->max_age === null) {
                            return 'Všetky';
                        }
                        if ($record->max_age === null) {
                            return $record->min_age.'+';
                        }
                        if ($record->min_age === null) {
                            return 'do '.$record->max_age;
                        }

                        return $record->min_age.'-'.$record->max_age;
                    }),
                TextColumn::make('pricing_type')
                    ->label('Typ ceny')
                    ->badge(),
                TextColumn::make('price_amount')
                    ->label('Cena')
                    ->money('EUR')
                    ->placeholder('-'),
                TextColumn::make('registrations_count')
                    ->counts('registrations')
                    ->label('Registrácie')
                    ->sortable(),
                IconColumn::make('is_active')
                    ->label('Aktívny')
                    ->boolean()
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
            ->filters([
                SelectFilter::make('sport_category_id')
                    ->relationship('sportCategory', 'id')
                    ->getOptionLabelFromRecordUsing(fn (Model $record): string => $record->getTranslation('name', 'sk'))
                    ->label('Športová kategória')
                    ->preload(),
                SelectFilter::make('pricing_type')
                    ->label('Typ ceny')
                    ->options(TrainingPricingTypeEnum::class),
                TernaryFilter::make('is_active')
                    ->label('Aktívny'),
            ])
            ->recordUrl(fn (Training $record): string => TrainingResource::getUrl('view', ['record' => $record]))
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
