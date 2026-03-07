<?php

namespace App\Filament\Resources\Sponsors\Schemas;

use App\Enums\SponsorTagEnum;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use RalphJSmit\Filament\MediaLibrary\Filament\Forms\Components\MediaPicker;

class SponsorForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Názov')
                    ->required(),
                Select::make('tag')
                    ->label('Typ')
                    ->options(SponsorTagEnum::class)
                    ->required(),
                MediaPicker::make('logo')
                    ->label('Logo'),
                TextInput::make('link')
                    ->label('Odkaz')
                    ->url(),
                Toggle::make('is_visible')
                    ->label('Viditeľný')
                    ->default(true),
                TextInput::make('sort_order')
                    ->label('Poradie')
                    ->numeric()
                    ->default(0),
            ]);
    }
}
