<?php

namespace App\Filament\Resources\FaqCategories\Schemas;

use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Schema;

class FaqCategoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
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
                        Tabs\Tab::make('CZ')
                            ->schema([
                                TextInput::make('title.cz')
                                    ->label('Názov (CZ)'),
                            ]),
                    ])
                    ->columnSpanFull(),
                Section::make('Vzhľad a poradie')
                    ->schema([
                        ColorPicker::make('color')
                            ->label('Farba'),
                        TextInput::make('icon')
                            ->label('Ikona')
                            ->placeholder('napr. heroicon-o-question-mark-circle'),
                        TextInput::make('sort_order')
                            ->label('Poradie')
                            ->numeric()
                            ->default(0),
                    ]),
            ]);
    }
}
