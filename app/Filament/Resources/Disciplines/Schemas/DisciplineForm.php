<?php

namespace App\Filament\Resources\Disciplines\Schemas;

use App\Enums\ScoringFormatEnum;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Schema;
use RalphJSmit\Filament\MediaLibrary\Filament\Forms\Components\MediaPicker;

class DisciplineForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Základné údaje')
                    ->schema([
                        Tabs::make('Preklady')
                            ->tabs([
                                Tabs\Tab::make('SK')
                                    ->schema([
                                        TextInput::make('name.sk')
                                            ->label('Názov (SK)')
                                            ->required(),
                                        Textarea::make('description.sk')
                                            ->label('Popis (SK)')
                                            ->rows(3),
                                        Textarea::make('scoring_description.sk')
                                            ->label('Popis hodnotenia (SK)')
                                            ->rows(2),
                                    ]),
                                Tabs\Tab::make('EN')
                                    ->schema([
                                        TextInput::make('name.en')
                                            ->label('Názov (EN)'),
                                        Textarea::make('description.en')
                                            ->label('Popis (EN)')
                                            ->rows(3),
                                        Textarea::make('scoring_description.en')
                                            ->label('Popis hodnotenia (EN)')
                                            ->rows(2),
                                    ]),
                                Tabs\Tab::make('CZ')
                                    ->schema([
                                        TextInput::make('name.cz')
                                            ->label('Názov (CZ)'),
                                        Textarea::make('description.cz')
                                            ->label('Popis (CZ)')
                                            ->rows(3),
                                        Textarea::make('scoring_description.cz')
                                            ->label('Popis hodnotenia (CZ)')
                                            ->rows(2),
                                    ]),
                            ])
                            ->columnSpanFull(),
                        Select::make('scoring_format')
                            ->label('Formát hodnotenia')
                            ->options(ScoringFormatEnum::class)
                            ->required(),
                        TextInput::make('icon')
                            ->label('Ikona')
                            ->placeholder('napr. heroicon-o-bolt'),
                        MediaPicker::make('image')
                            ->label('Obrázok'),
                        TextInput::make('sort_order')
                            ->label('Poradie')
                            ->numeric()
                            ->default(0),
                    ]),
            ]);
    }
}
