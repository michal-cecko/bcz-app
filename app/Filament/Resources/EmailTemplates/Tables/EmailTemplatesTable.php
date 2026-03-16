<?php

namespace App\Filament\Resources\EmailTemplates\Tables;

use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class EmailTemplatesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->emptyStateHeading('Žiadne šablóny')
            ->emptyStateDescription('Vytvorte svoju prvú e-mailovú šablónu.')
            ->columns([
                TextColumn::make('name')
                    ->label('Názov')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('subject')
                    ->label('Predmet')
                    ->searchable()
                    ->limit(50),
                TextColumn::make('updated_at')
                    ->label('Upravené')
                    ->dateTime()
                    ->sortable(),
            ])
            ->defaultSort('updated_at', 'desc');
    }
}
