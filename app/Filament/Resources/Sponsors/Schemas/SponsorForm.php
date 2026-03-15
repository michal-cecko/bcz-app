<?php

namespace App\Filament\Resources\Sponsors\Schemas;

use App\Enums\SponsorTagEnum;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

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
                SpatieMediaLibraryFileUpload::make('logo')
                    ->collection('logo')
                    ->disk('public')
                    ->visibility('public')
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
