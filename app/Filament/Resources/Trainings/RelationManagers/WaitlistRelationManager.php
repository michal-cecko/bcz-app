<?php

namespace App\Filament\Resources\Trainings\RelationManagers;

use App\Models\Training;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class WaitlistRelationManager extends RelationManager
{
    protected static string $relationship = 'waitlistEntries';

    protected static ?string $title = 'Čakací zoznam';

    protected static ?string $modelLabel = 'záznam';

    protected static ?string $pluralModelLabel = 'Čakací zoznam';

    public static function canViewForRecord(\Illuminate\Database\Eloquent\Model $ownerRecord, string $pageClass): bool
    {
        /** @var Training $ownerRecord */
        return (bool) $ownerRecord->notify_on_available;
    }

    public function table(Table $table): Table
    {
        return $table
            ->emptyStateHeading('Žiadni čakajúci')
            ->emptyStateDescription('Nikto zatiaľ nečaká na voľné miesto.')
            ->columns([
                TextColumn::make('user.name')
                    ->label('Používateľ')
                    ->placeholder('Hosť')
                    ->searchable(),
                TextColumn::make('user.email')
                    ->label('E-mail')
                    ->placeholder('-')
                    ->searchable(),
                TextColumn::make('created_at')
                    ->label('Zapísaný')
                    ->dateTime()
                    ->sortable(),
            ])
            ->recordActions([
                DeleteAction::make()
                    ->modalHeading('Odstrániť z čakacieho zoznamu'),
            ])
            ->toolbarActions([
                DeleteBulkAction::make(),
            ]);
    }
}
