<?php

namespace App\Filament\Resources\FaqCategories\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Schema;
use Guava\IconPicker\Forms\Components\IconPicker;

class FaqCategoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
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
                                TextInput::make('title.cs')
                                    ->label('Názov (CZ)'),
                            ]),
                    ])
                    ->columnSpanFull(),
                Section::make('Vzhľad a poradie')
                    ->schema([
                        Select::make('color')
                            ->label('Farba')
                            ->options([
                                '#6366f1' => 'Fialová',
                                '#3b82f6' => 'Modrá',
                                '#22c55e' => 'Zelená',
                                '#f59e0b' => 'Žltá',
                                '#ef4444' => 'Červená',
                                '#6b7280' => 'Sivá',
                                '#ec4899' => 'Ružová',
                                '#f97316' => 'Oranžová',
                                '#14b8a6' => 'Teal (tyrkysová)',
                            ])
                            ->searchable(),
                        IconPicker::make('icon')
                            ->label('Ikona')
                            ->sets(['heroicons'])
                            ->columns(3),
                        TextInput::make('sort_order')
                            ->label('Poradie')
                            ->numeric()
                            ->default(0),
                    ]),
            ]);
    }
}
