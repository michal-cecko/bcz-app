<?php

namespace App\Filament\Resources\Competitions\RelationManagers;

use App\Enums\RoundAdvancementTypeEnum;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class RoundsRelationManager extends RelationManager
{
    protected static string $relationship = 'rounds';

    protected static ?string $title = 'Kolá';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Názov')
                    ->required()
                    ->placeholder('napr. Kvalifikácia, Semifinále, Finále'),
                TextInput::make('round_number')
                    ->label('Číslo kola')
                    ->numeric()
                    ->required(),
                Select::make('athlete_category_id')
                    ->label('Kategória')
                    ->relationship(name: 'athleteCategory')
                    ->getOptionLabelFromRecordUsing(fn (Model $record): string => $record->getTranslation('name', 'sk'))
                    ->preload()
                    ->searchable(['name->sk'])
                    ->placeholder('Všetky kategórie'),
                Select::make('advancement_type')
                    ->label('Typ postupu')
                    ->options(RoundAdvancementTypeEnum::class)
                    ->required()
                    ->live(),
                TextInput::make('advance_count')
                    ->numeric()
                    ->visible(fn (Get $get): bool => $get('advancement_type') === RoundAdvancementTypeEnum::TOP_BY_POINTS->value)
                    ->label('Počet postupujúcich'),
                TextInput::make('battle_size')
                    ->label('Veľkosť battlu')
                    ->numeric()
                    ->visible(fn (Get $get): bool => $get('advancement_type') === RoundAdvancementTypeEnum::BATTLE_WINNER->value)
                    ->placeholder('1 = 1v1, 2 = 2v2'),
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
                TextColumn::make('round_number')
                    ->label('#'),
                TextColumn::make('name')
                    ->label('Názov')
                    ->searchable(),
                TextColumn::make('athleteCategory.name')
                    ->label('Kategória')
                    ->placeholder('Všetky'),
                TextColumn::make('advancement_type')
                    ->label('Typ postupu')
                    ->badge(),
                TextColumn::make('advance_count')
                    ->label('Postupujúcich')
                    ->placeholder('-'),
                TextColumn::make('battle_size')
                    ->label('Veľkosť battlu')
                    ->placeholder('-'),
                TextColumn::make('parts_count')
                    ->counts('parts')
                    ->label('Časti'),
                TextColumn::make('battles_count')
                    ->counts('battles')
                    ->label('Boje'),
            ]);
    }
}
