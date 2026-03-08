<?php

namespace App\Filament\Resources\Competitions\RelationManagers;

use App\Models\Discipline;
use Filament\Forms\Components\Select;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class JudgesRelationManager extends RelationManager
{
    protected static string $relationship = 'judges';

    protected static ?string $title = 'Rozhodcovia';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('recordId')
                    ->label('Rozhodca')
                    ->relationship('judges', 'name')
                    ->preload()
                    ->searchable()
                    ->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Meno')
                    ->searchable(),
                TextColumn::make('email')
                    ->label('Email')
                    ->searchable(),
                TextColumn::make('pivot.discipline_id')
                    ->label('Disciplína')
                    ->formatStateUsing(function (?string $state): string {
                        if (! $state) {
                            return '-';
                        }

                        $discipline = Discipline::find($state);

                        return $discipline ? $discipline->getTranslation('name', 'sk') : '-';
                    })
                    ->placeholder('-'),
            ])
            ->headerActions([
                \Filament\Tables\Actions\AttachAction::make()
                    ->preloadRecordSelect()
                    ->recordSelectSearchColumns(['name', 'email'])
                    ->form(fn (\Filament\Tables\Actions\AttachAction $action): array => [
                        $action->getRecordSelect(),
                        Select::make('discipline_id')
                            ->label('Disciplína')
                            ->options(fn () => Discipline::all()->mapWithKeys(fn (Discipline $record) => [$record->id => $record->getTranslation('name', 'sk')]))
                            ->placeholder('Všetky disciplíny')
                            ->preload()
                            ->searchable(),
                    ]),
            ])
            ->actions([
                \Filament\Tables\Actions\DetachAction::make(),
            ]);
    }
}
