<?php

namespace App\Filament\Resources\Competitions\RelationManagers;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

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
                    ->preload()
                    ->searchable()
                    ->required(),
                Select::make('athlete_category_id')
                    ->label('Kategória')
                    ->relationship(name: 'athleteCategory')
                    ->getOptionLabelFromRecordUsing(fn (Model $record): string => $record->getTranslation('name', 'sk'))
                    ->preload()
                    ->searchable(['name->sk']),
                Select::make('status')
                    ->label('Stav')
                    ->options([
                        'pending' => 'Čakajúca',
                        'confirmed' => 'Potvrdená',
                        'cancelled' => 'Zrušená',
                    ])
                    ->default('pending')
                    ->required(),
                TextInput::make('weight_in')
                    ->label('Váha')
                    ->numeric()
                    ->suffix('kg'),
                DateTimePicker::make('registered_at')
                    ->label('Registrovaný')
                    ->default(now()),
                KeyValue::make('form_data')
                    ->label('Dáta formulára')
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')
                    ->label('Používateľ')
                    ->searchable(),
                TextColumn::make('athleteCategory.name')
                    ->label('Kategória')
                    ->placeholder('-'),
                TextColumn::make('status')
                    ->label('Stav')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'pending' => 'Čakajúca',
                        'confirmed' => 'Potvrdená',
                        'cancelled' => 'Zrušená',
                        default => $state,
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'confirmed' => 'success',
                        'cancelled' => 'danger',
                        default => 'gray',
                    }),
                TextColumn::make('weight_in')
                    ->label('Váha')
                    ->suffix(' kg')
                    ->placeholder('-'),
                TextColumn::make('registered_at')
                    ->label('Registrovaný')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('form_data')
                    ->label('Dáta formulára')
                    ->formatStateUsing(function (mixed $state): string {
                        if (empty($state)) {
                            return '-';
                        }

                        return collect($state)
                            ->map(fn ($value, $key) => "{$key}: {$value}")
                            ->implode(', ');
                    })
                    ->placeholder('-')
                    ->wrap()
                    ->toggleable(isToggledHiddenByDefault: true),
            ]);
    }
}
