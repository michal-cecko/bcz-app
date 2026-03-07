<?php

namespace App\Filament\Resources\Trainings\RelationManagers;

use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Forms\Components\Select;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class RegistrationsRelationManager extends RelationManager
{
    protected static string $relationship = 'registrations';

    protected static ?string $title = 'Registrácie';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('user_id')
                    ->label('Používateľ')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->preload(),
                Select::make('status')
                    ->label('Stav')
                    ->options([
                        'pending' => 'Čakajúca',
                        'approved' => 'Schválená',
                        'rejected' => 'Zamietnutá',
                        'cancelled' => 'Zrušená',
                    ])
                    ->required()
                    ->default('pending'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')
                    ->label('Používateľ')
                    ->placeholder('Hosť'),
                TextColumn::make('status')
                    ->label('Stav')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'pending' => 'Čakajúca',
                        'approved' => 'Schválená',
                        'rejected' => 'Zamietnutá',
                        'cancelled' => 'Zrušená',
                        default => $state,
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'approved' => 'success',
                        'pending' => 'warning',
                        'rejected' => 'danger',
                        'cancelled' => 'gray',
                        default => 'gray',
                    }),
                TextColumn::make('registered_at')
                    ->label('Registrovaný')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('Vytvorený')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->headerActions([
                CreateAction::make(),
            ])
            ->recordActions([
                DeleteAction::make(),
            ])
            ->toolbarActions([
                DeleteBulkAction::make(),
            ]);
    }
}
