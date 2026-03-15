<?php

namespace App\Filament\Resources\Events\RelationManagers;

use App\Enums\EventTypeEnum;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DateTimePicker;
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

    public static function canViewForRecord(Model $ownerRecord, string $pageClass): bool
    {
        return in_array($ownerRecord->event_type, [EventTypeEnum::Organized, EventTypeEnum::Competition]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('user_id')
                ->label('Používateľ')
                ->relationship('user', 'name')
                ->searchable()
                ->preload(),
            Select::make('athlete_category_id')
                ->label('Kategória')
                ->relationship('athleteCategory')
                ->getOptionLabelFromRecordUsing(fn (Model $record): string => $record->getTranslation('name', 'sk'))
                ->preload()
                ->visible(fn (): bool => $this->getOwnerRecord()->event_type === EventTypeEnum::Competition),
            Select::make('status')
                ->label('Stav')
                ->options([
                    'pending' => 'Čakajúci',
                    'confirmed' => 'Potvrdený',
                    'cancelled' => 'Zrušený',
                ])
                ->default('pending')
                ->required(),
            TextInput::make('weight_in')
                ->label('Váha (kg)')
                ->numeric()
                ->visible(fn (): bool => $this->getOwnerRecord()->event_type === EventTypeEnum::Competition),
            DateTimePicker::make('registered_at')
                ->label('Dátum registrácie')
                ->default(now()),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->defaultSort('registered_at', 'desc')
            ->columns([
                TextColumn::make('user.name')
                    ->label('Meno')
                    ->searchable(),
                TextColumn::make('athleteCategory.name')
                    ->label('Kategória')
                    ->state(fn ($record): ?string => $record->athleteCategory?->getTranslation('name', 'sk'))
                    ->placeholder('-')
                    ->visible(fn (): bool => $this->getOwnerRecord()->event_type === EventTypeEnum::Competition),
                TextColumn::make('status')
                    ->label('Stav')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'confirmed' => 'success',
                        'pending' => 'warning',
                        'cancelled' => 'danger',
                        default => 'gray',
                    }),
                TextColumn::make('weight_in')
                    ->label('Váha')
                    ->suffix(' kg')
                    ->placeholder('-')
                    ->visible(fn (): bool => $this->getOwnerRecord()->event_type === EventTypeEnum::Competition),
                TextColumn::make('registered_at')
                    ->label('Registrovaný')
                    ->dateTime()
                    ->sortable(),
            ])
            ->headerActions([
                CreateAction::make(),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }
}
