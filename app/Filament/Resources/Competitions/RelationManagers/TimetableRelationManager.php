<?php

namespace App\Filament\Resources\Competitions\RelationManagers;

use App\Enums\TimetableEntryStatusEnum;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class TimetableRelationManager extends RelationManager
{
    protected static string $relationship = 'timetableEntries';

    protected static ?string $title = 'Harmonogram';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('Preklady názvu')
                    ->tabs([
                        Tabs\Tab::make('SK')
                            ->schema([
                                TextInput::make('title.sk')
                                    ->label('Názov (SK)')
                                    ->required(),
                            ]),
                        Tabs\Tab::make('EN')
                            ->schema([
                                TextInput::make('title.en')
                                    ->label('Názov (EN)'),
                            ]),
                    ])
                    ->columnSpanFull(),
                DateTimePicker::make('scheduled_time')
                    ->label('Plánovaný čas')
                    ->required(),
                DateTimePicker::make('actual_start_time')
                    ->label('Skutočný začiatok'),
                DateTimePicker::make('actual_end_time')
                    ->label('Skutočný koniec'),
                Select::make('status')
                    ->label('Stav')
                    ->options(TimetableEntryStatusEnum::class)
                    ->default(TimetableEntryStatusEnum::PENDING)
                    ->required(),
                TextInput::make('sort_order')
                    ->label('Poradie')
                    ->numeric()
                    ->default(0),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->reorderable('sort_order')
            ->defaultSort('sort_order')
            ->columns([
                TextColumn::make('title')
                    ->label('Názov')
                    ->searchable(),
                TextColumn::make('scheduled_time')
                    ->label('Plánovaný čas')
                    ->dateTime('H:i')
                    ->sortable(),
                TextColumn::make('actual_start_time')
                    ->label('Skutočný začiatok')
                    ->dateTime('H:i')
                    ->placeholder('-'),
                TextColumn::make('actual_end_time')
                    ->label('Skutočný koniec')
                    ->dateTime('H:i')
                    ->placeholder('-'),
                TextColumn::make('status')
                    ->label('Stav')
                    ->badge()
                    ->color(fn (TimetableEntryStatusEnum $state): string => match ($state) {
                        TimetableEntryStatusEnum::PENDING => 'gray',
                        TimetableEntryStatusEnum::IN_PROGRESS => 'warning',
                        TimetableEntryStatusEnum::FINISHED => 'success',
                    }),
            ]);
    }
}
