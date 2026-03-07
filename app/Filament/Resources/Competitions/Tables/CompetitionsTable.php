<?php

namespace App\Filament\Resources\Competitions\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class CompetitionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('date_start', 'desc')
            ->columns([
                TextColumn::make('name')
                    ->label('Názov')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('organizerTeam.name')
                    ->label('Organizátor')
                    ->state(fn ($record): ?string => $record->organizerTeam?->getTranslation('name', 'sk'))
                    ->placeholder('-'),
                TextColumn::make('date_start')
                    ->label('Začiatok')
                    ->date()
                    ->sortable(),
                TextColumn::make('date_end')
                    ->label('Koniec')
                    ->date()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('city')
                    ->label('Mesto')
                    ->searchable(),
                TextColumn::make('registrations_count')
                    ->counts('registrations')
                    ->label('Registrácie')
                    ->sortable(),
                IconColumn::make('is_published')
                    ->label('Publikované')
                    ->boolean()
                    ->sortable(),
                TextColumn::make('status')
                    ->label('Stav')
                    ->badge()
                    ->sortable()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'hidden' => 'Skrytá',
                        'countdown' => 'Odpočet',
                        'registering' => 'Registrácia',
                        'upcoming' => 'Nadchádzajúca',
                        'in_progress' => 'Prebieha',
                        'finished' => 'Ukončená',
                        default => $state,
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'hidden' => 'gray',
                        'countdown' => 'info',
                        'registering' => 'success',
                        'upcoming' => 'primary',
                        'in_progress' => 'warning',
                        'finished' => 'gray',
                        default => 'gray',
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
                TernaryFilter::make('is_published')
                    ->label('Publikované'),
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
