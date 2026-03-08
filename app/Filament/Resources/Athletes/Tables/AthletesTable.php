<?php

namespace App\Filament\Resources\Athletes\Tables;

use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class AthletesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Meno')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('email')
                    ->label('Email')
                    ->searchable(),
                TextColumn::make('athleteProfile.date_started_working_out')
                    ->label('Začiatok tréningu')
                    ->date()
                    ->placeholder('-')
                    ->sortable(),
                TextColumn::make('competitionRegistrations_count')
                    ->label('Registrácie')
                    ->counts('competitionRegistrations')
                    ->sortable(),
            ]);
    }
}
