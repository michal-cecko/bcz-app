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
                                TextInput::make('title.cz')
                                    ->label('Názov (CZ)'),
                            ]),
                    ])
                    ->columnSpanFull(),
                Section::make('Vzhľad a poradie')
                    ->schema([
                        Select::make('color')
                            ->label('Farba')
                            ->options([
                                '#6366f1' => 'Primary (fialová)',
                                '#3b82f6' => 'Info (modrá)',
                                '#22c55e' => 'Success (zelená)',
                                '#f59e0b' => 'Warning (žltá)',
                                '#ef4444' => 'Danger (červená)',
                                '#6b7280' => 'Gray (sivá)',
                                '#ec4899' => 'Pink (ružová)',
                                '#f97316' => 'Orange (oranžová)',
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
