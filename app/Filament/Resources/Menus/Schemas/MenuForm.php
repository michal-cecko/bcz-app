<?php

namespace App\Filament\Resources\Menus\Schemas;

use App\Enums\MenuLocationEnum;
use App\Mason\Support\LinkPickerField;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Schema;

class MenuForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Menu')
                    ->schema([
                        Select::make('location')
                            ->label('Umiestnenie')
                            ->options(MenuLocationEnum::class)
                            ->required()
                            ->disabled(fn ($record): bool => $record !== null),
                        Tabs::make('Preklady')
                            ->tabs([
                                Tabs\Tab::make('SK')
                                    ->schema([
                                        TextInput::make('label.sk')
                                            ->label('Názov (SK)')
                                            ->required(),
                                    ]),
                                Tabs\Tab::make('EN')
                                    ->schema([
                                        TextInput::make('label.en')
                                            ->label('Názov (EN)'),
                                    ]),
                                Tabs\Tab::make('CZ')
                                    ->schema([
                                        TextInput::make('label.cz')
                                            ->label('Názov (CZ)'),
                                    ]),
                            ])
                            ->columnSpanFull(),
                        Repeater::make('items')
                            ->label('Položky menu')
                            ->schema([
                                TextInput::make('label_sk')
                                    ->label('Názov (SK)')
                                    ->required(),
                                TextInput::make('label_en')
                                    ->label('Názov (EN)'),
                                TextInput::make('label_cz')
                                    ->label('Názov (CZ)'),
                                LinkPickerField::make(),
                                Select::make('target')
                                    ->label('Cieľ')
                                    ->options([
                                        '_self' => 'Rovnaké okno',
                                        '_blank' => 'Nové okno',
                                    ])
                                    ->default('_self'),
                                TextInput::make('sort_order')
                                    ->label('Poradie')
                                    ->numeric()
                                    ->default(0),
                            ])
                            ->reorderable()
                            ->reorderableWithButtons()
                            ->cloneable()
                            ->collapsible()
                            ->defaultItems(0)
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
