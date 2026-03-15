<?php

namespace App\Filament\Resources\Events\RelationManagers;

use App\Enums\EventTypeEnum;
use App\Enums\RoundAdvancementTypeEnum;
use App\Enums\ScoringFormatEnum;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
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
    protected static string $relationship = 'competitionDetail';

    protected static ?string $title = 'Kolá';

    public static function canViewForRecord(Model $ownerRecord, string $pageClass): bool
    {
        return $ownerRecord->event_type === EventTypeEnum::Competition;
    }

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('name')
                ->label('Názov')
                ->required(),
            TextInput::make('round_number')
                ->label('Číslo kola')
                ->numeric()
                ->required(),
            Select::make('athlete_category_id')
                ->label('Kategória')
                ->relationship('athleteCategory')
                ->getOptionLabelFromRecordUsing(fn (Model $record): string => $record->getTranslation('name', 'sk'))
                ->preload()
                ->searchable(),
            Select::make('scoring_format')
                ->label('Formát hodnotenia')
                ->options(ScoringFormatEnum::class),
            Select::make('advancement_type')
                ->label('Typ postupu')
                ->options(RoundAdvancementTypeEnum::class)
                ->required()
                ->live(),
            TextInput::make('advance_count')
                ->label('Počet postupujúcich')
                ->numeric()
                ->visible(fn (Get $get): bool => $get('advancement_type') === RoundAdvancementTypeEnum::TOP_BY_POINTS->value),
            TextInput::make('battle_size')
                ->label('Veľkosť battle')
                ->numeric()
                ->visible(fn (Get $get): bool => $get('advancement_type') === RoundAdvancementTypeEnum::BATTLE_WINNER->value),
            TextInput::make('sort_order')
                ->label('Poradie')
                ->numeric()
                ->default(0),
        ]);
    }

    public function table(Table $table): Table
    {
        $detail = $this->getOwnerRecord()->competitionDetail;

        if (! $detail) {
            return $table->columns([]);
        }

        return $table
            ->relationship(fn () => $detail->rounds())
            ->reorderable('sort_order')
            ->defaultSort('sort_order')
            ->columns([
                TextColumn::make('round_number')
                    ->label('#')
                    ->sortable(),
                TextColumn::make('name')
                    ->label('Názov')
                    ->searchable(),
                TextColumn::make('athleteCategory.name')
                    ->label('Kategória')
                    ->state(fn ($record): ?string => $record->athleteCategory?->getTranslation('name', 'sk'))
                    ->placeholder('-'),
                TextColumn::make('advancement_type')
                    ->label('Postup')
                    ->badge(),
                TextColumn::make('parts_count')
                    ->label('Časti')
                    ->counts('parts'),
                TextColumn::make('battles_count')
                    ->label('Battle')
                    ->counts('battles'),
            ])
            ->headerActions([
                CreateAction::make(),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }
}
