<?php

namespace App\Filament\Resources\Events\RelationManagers;

use App\Enums\EventTypeEnum;
use App\Enums\TimetableEntryStatusEnum;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class TimetableRelationManager extends RelationManager
{
    protected static string $relationship = 'competitionDetail';

    protected static ?string $title = 'Harmonogram';

    public static function canViewForRecord(Model $ownerRecord, string $pageClass): bool
    {
        return $ownerRecord->event_type === EventTypeEnum::Competition;
    }

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Tabs::make('Preklady')
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
        $detail = $this->getOwnerRecord()->competitionDetail;

        if (! $detail) {
            return $table->columns([]);
        }

        return $table
            ->relationship(fn () => $detail->timetableEntries())
            ->reorderable('sort_order')
            ->defaultSort('sort_order')
            ->columns([
                TextColumn::make('title')
                    ->label('Názov')
                    ->searchable(),
                TextColumn::make('scheduled_time')
                    ->label('Plánovaný čas')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('actual_start_time')
                    ->label('Začiatok')
                    ->dateTime()
                    ->placeholder('-'),
                TextColumn::make('actual_end_time')
                    ->label('Koniec')
                    ->dateTime()
                    ->placeholder('-'),
                TextColumn::make('status')
                    ->label('Stav')
                    ->badge(),
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
