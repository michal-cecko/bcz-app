<?php

namespace App\Filament\Resources\Settings\Schemas;

use App\Enums\SettingTypeEnum;
use App\Models\Setting;
use App\Models\Team;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class SettingForm
{
    public static function configure(Schema $schema): Schema
    {
        /** @var Setting|null $record */
        $record = $schema->getRecord();
        $type = $record?->type;

        return $schema
            ->components([
                Section::make()
                    ->schema([
                        TextInput::make('label')
                            ->label('Názov')
                            ->disabled()
                            ->dehydrated(false)
                            ->formatStateUsing(fn ($state) => is_array($state) ? ($state[app()->getLocale()] ?? $state['sk'] ?? reset($state)) : $state),
                        TextInput::make('description')
                            ->label('Popis')
                            ->disabled()
                            ->dehydrated(false)
                            ->formatStateUsing(fn ($state) => is_array($state) ? ($state[app()->getLocale()] ?? $state['sk'] ?? reset($state)) : $state),
                        TextInput::make('key')
                            ->label('Kľúč')
                            ->disabled()
                            ->dehydrated(false),
                        TextInput::make('type_display')
                            ->label('Typ')
                            ->disabled()
                            ->dehydrated(false)
                            ->formatStateUsing(fn () => $type?->translation() ?? $type?->value),

                        // Dynamic value field based on the record's stored type.
                        // Only ONE value field is rendered per setting so there's no name
                        // collision that could overwrite state with null on submit.
                        ...self::valueFieldFor($type),
                    ]),
            ]);
    }

    /** @return list<Component> */
    private static function valueFieldFor(?SettingTypeEnum $type): array
    {
        return match ($type) {
            SettingTypeEnum::NUMBER => [
                TextInput::make('value')->label('Hodnota')->numeric(),
            ],
            SettingTypeEnum::BOOLEAN => [
                Toggle::make('value')->label('Hodnota'),
            ],
            SettingTypeEnum::SELECT => [
                Select::make('value')
                    ->label('Hodnota')
                    ->options(fn ($record) => collect($record?->options ?? [])->mapWithKeys(fn ($v) => [$v => $v])),
            ],
            SettingTypeEnum::MULTI_SELECT => [
                Select::make('value')
                    ->label('Hodnota')
                    ->multiple()
                    ->options(fn ($record) => collect($record?->options ?? [])->mapWithKeys(fn ($v) => [$v => $v])),
            ],
            SettingTypeEnum::TEAM_SELECT => [
                Select::make('value')
                    ->label('Hodnota')
                    ->options(fn () => Team::query()->pluck('name', 'id')->toArray())
                    ->searchable(),
            ],
            SettingTypeEnum::DATE => [
                DatePicker::make('value')->label('Hodnota')->nullable(),
            ],
            default => [
                TextInput::make('value')->label('Hodnota'),
            ],
        };
    }
}
