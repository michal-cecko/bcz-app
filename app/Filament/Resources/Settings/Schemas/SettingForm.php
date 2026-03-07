<?php

namespace App\Filament\Resources\Settings\Schemas;

use App\Enums\SettingTypeEnum;
use App\Models\Team;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;

class SettingForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->schema([
                        TextInput::make('label')
                            ->label('Názov')
                            ->disabled()
                            ->dehydrated(false),
                        TextInput::make('description')
                            ->label('Popis')
                            ->disabled()
                            ->dehydrated(false),
                        TextInput::make('type')
                            ->label('Typ')
                            ->disabled()
                            ->dehydrated(false)
                            ->formatStateUsing(fn ($state) => $state instanceof SettingTypeEnum ? $state->translation() : $state),

                        // Dynamic value field based on type
                        TextInput::make('value')
                            ->label('Hodnota')
                            ->visible(fn (Get $get): bool => in_array($get('type'), [SettingTypeEnum::TEXT, SettingTypeEnum::TEXT->value, null])),

                        TextInput::make('value')
                            ->label('Hodnota')
                            ->numeric()
                            ->visible(fn (Get $get): bool => in_array($get('type'), [SettingTypeEnum::NUMBER, SettingTypeEnum::NUMBER->value])),

                        Toggle::make('value')
                            ->label('Hodnota')
                            ->visible(fn (Get $get): bool => in_array($get('type'), [SettingTypeEnum::BOOLEAN, SettingTypeEnum::BOOLEAN->value])),

                        Select::make('value')
                            ->label('Hodnota')
                            ->options(fn ($record) => collect($record?->options ?? [])->mapWithKeys(fn ($v) => [$v => $v]))
                            ->visible(fn (Get $get): bool => in_array($get('type'), [SettingTypeEnum::SELECT, SettingTypeEnum::SELECT->value])),

                        Select::make('value')
                            ->label('Hodnota')
                            ->multiple()
                            ->options(fn ($record) => collect($record?->options ?? [])->mapWithKeys(fn ($v) => [$v => $v]))
                            ->visible(fn (Get $get): bool => in_array($get('type'), [SettingTypeEnum::MULTI_SELECT, SettingTypeEnum::MULTI_SELECT->value])),

                        Select::make('value')
                            ->label('Hodnota')
                            ->options(fn () => Team::query()->pluck('name', 'id')->toArray())
                            ->searchable()
                            ->visible(fn (Get $get): bool => in_array($get('type'), [SettingTypeEnum::TEAM_SELECT, SettingTypeEnum::TEAM_SELECT->value])),
                    ]),
            ]);
    }
}
