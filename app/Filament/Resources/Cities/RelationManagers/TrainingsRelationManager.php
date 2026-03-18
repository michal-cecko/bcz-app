<?php

namespace App\Filament\Resources\Cities\RelationManagers;

use App\Filament\Resources\Trainings\TrainingResource;
use App\Models\Training;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ViewAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class TrainingsRelationManager extends RelationManager
{
    protected static string $relationship = 'trainings';

    protected static ?string $title = 'Tréningy';

    protected static ?string $modelLabel = 'tréning';

    protected static ?string $pluralModelLabel = 'Tréningy';

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->label('Názov')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('sportCategory.name')
                    ->label('Šport')
                    ->state(fn (Training $record): ?string => $record->sportCategory?->getTranslation('name', 'sk')),
                TextColumn::make('pricing_type')
                    ->label('Typ ceny')
                    ->badge(),
                TextColumn::make('registrations_count')
                    ->counts('registrations')
                    ->label('Registrácie'),
                IconColumn::make('is_active')
                    ->label('Aktívny')
                    ->boolean(),
            ])
            ->defaultSort('sort_order')
            ->recordUrl(fn (Training $record): string => TrainingResource::getUrl('view', ['record' => $record]))
            ->recordActions([
                ViewAction::make()
                    ->url(fn (Training $record): string => TrainingResource::getUrl('view', ['record' => $record])),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
