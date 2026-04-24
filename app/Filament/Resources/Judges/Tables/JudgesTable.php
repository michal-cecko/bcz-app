<?php

namespace App\Filament\Resources\Judges\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class JudgesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                SpatieMediaLibraryImageColumn::make('profile_image')
                    ->collection('profile_image')
                    ->label('Foto')
                    ->circular(),
                TextColumn::make('name')
                    ->label('Meno')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('disciplines')
                    ->label('Disciplíny')
                    ->badge()
                    ->formatStateUsing(fn ($state) => is_array($state) ? implode(', ', $state) : $state),
                TextColumn::make('date_started_judging')
                    ->label('Od')
                    ->date('Y')
                    ->sortable(),
                TextColumn::make('judged_competition_details_count')
                    ->label('Súťaží')
                    ->counts('judgedCompetitionDetails'),
            ])
            ->defaultSort('name')
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
