<?php

namespace App\Filament\Resources\EventCategories\Schemas;

use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Repeater\TableColumn;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Schema;
use Guava\IconPicker\Forms\Components\IconPicker;
use RalphJSmit\Filament\MediaLibrary\Filament\Forms\Components\MediaPicker;

class EventCategoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                Section::make('Základné údaje')
                    ->schema([
                        Tabs::make('Preklady')
                            ->tabs([
                                Tabs\Tab::make('SK')
                                    ->schema([
                                        TextInput::make('title.sk')
                                            ->label('Názov (SK)')
                                            ->required(),
                                        TextInput::make('card_subtitle.sk')
                                            ->label('Podtitulok karty (SK)'),
                                        Textarea::make('card_description.sk')
                                            ->label('Popis karty (SK)')
                                            ->rows(2),
                                    ]),
                                Tabs\Tab::make('EN')
                                    ->schema([
                                        TextInput::make('title.en')
                                            ->label('Názov (EN)'),
                                        TextInput::make('card_subtitle.en')
                                            ->label('Podtitulok karty (EN)'),
                                        Textarea::make('card_description.en')
                                            ->label('Popis karty (EN)')
                                            ->rows(2),
                                    ]),
                                Tabs\Tab::make('CZ')
                                    ->schema([
                                        TextInput::make('title.cz')
                                            ->label('Názov (CZ)'),
                                        TextInput::make('card_subtitle.cz')
                                            ->label('Podtitulok karty (CZ)'),
                                        Textarea::make('card_description.cz')
                                            ->label('Popis karty (CZ)')
                                            ->rows(2),
                                    ]),
                            ])
                            ->columnSpanFull(),
                        TextInput::make('slug')
                            ->disabled()
                            ->dehydrated(),
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
                        Toggle::make('is_active')
                            ->label('Aktívna')
                            ->default(true),
                        TextInput::make('sort_order')
                            ->label('Poradie')
                            ->numeric()
                            ->default(0),
                    ]),

                Section::make('Obrázky')
                    ->schema([
                        MediaPicker::make('card_image')
                            ->label('Obrázok karty'),
                        MediaPicker::make('detail_image')
                            ->label('Obrázok detailu'),
                        MediaPicker::make('hero_image')
                            ->label('Hero obrázok'),
                        MediaPicker::make('about_image')
                            ->label('O nás obrázok'),
                    ])
                    ->columns(2),

                Section::make('Obsah detailovej stránky')
                    ->schema([
                        Tabs::make('Preklady detailu')
                            ->tabs([
                                Tabs\Tab::make('SK')
                                    ->schema([
                                        TextInput::make('detail_title.sk')
                                            ->label('Titulok detailu (SK)'),
                                        TextInput::make('about_title.sk')
                                            ->label('Titulok O nás (SK)'),
                                        Textarea::make('about_description.sk')
                                            ->label('Popis O nás (SK)')
                                            ->rows(3),
                                        TextInput::make('types_section_title.sk')
                                            ->label('Titulok sekcie typov (SK)'),
                                        TextInput::make('types_section_subtitle.sk')
                                            ->label('Podtitulok sekcie typov (SK)'),
                                        TextInput::make('cta_title.sk')
                                            ->label('CTA titulok (SK)'),
                                        Textarea::make('cta_description.sk')
                                            ->label('CTA popis (SK)')
                                            ->rows(2),
                                    ]),
                                Tabs\Tab::make('EN')
                                    ->schema([
                                        TextInput::make('detail_title.en')
                                            ->label('Titulok detailu (EN)'),
                                        TextInput::make('about_title.en')
                                            ->label('Titulok O nás (EN)'),
                                        Textarea::make('about_description.en')
                                            ->label('Popis O nás (EN)')
                                            ->rows(3),
                                        TextInput::make('types_section_title.en')
                                            ->label('Titulok sekcie typov (EN)'),
                                        TextInput::make('types_section_subtitle.en')
                                            ->label('Podtitulok sekcie typov (EN)'),
                                        TextInput::make('cta_title.en')
                                            ->label('CTA titulok (EN)'),
                                        Textarea::make('cta_description.en')
                                            ->label('CTA popis (EN)')
                                            ->rows(2),
                                    ]),
                                Tabs\Tab::make('CZ')
                                    ->schema([
                                        TextInput::make('detail_title.cz')
                                            ->label('Titulok detailu (CZ)'),
                                        TextInput::make('about_title.cz')
                                            ->label('Titulok O nás (CZ)'),
                                        Textarea::make('about_description.cz')
                                            ->label('Popis O nás (CZ)')
                                            ->rows(3),
                                        TextInput::make('types_section_title.cz')
                                            ->label('Titulok sekcie typov (CZ)'),
                                        TextInput::make('types_section_subtitle.cz')
                                            ->label('Podtitulok sekcie typov (CZ)'),
                                        TextInput::make('cta_title.cz')
                                            ->label('CTA titulok (CZ)'),
                                        Textarea::make('cta_description.cz')
                                            ->label('CTA popis (CZ)')
                                            ->rows(2),
                                    ]),
                            ])
                            ->columnSpanFull(),
                    ])
                    ->collapsible(),

                Section::make('Karty typov')
                    ->description('Karty zobrazené v sekcii typov')
                    ->schema([
                        Repeater::make('types_cards')
                            ->label('Karty')
                            ->table([
                                TableColumn::make('Názov'),
                                TableColumn::make('Popis'),
                                TableColumn::make('Ikona'),
                            ])
                            ->schema([
                                TextInput::make('title')
                                    ->label('Názov')
                                    ->required(),
                                TextInput::make('description')
                                    ->label('Popis'),
                                IconPicker::make('icon')
                                    ->label('Ikona')
                                    ->sets(['heroicons'])
                                    ->columns(3),
                            ])
                            ->defaultItems(0)
                            ->reorderable()
                            ->columnSpanFull(),
                    ])
                    ->collapsible(),

                Section::make('Štatistiky')
                    ->description('Štatistiky zobrazené na stránke kategórie')
                    ->schema([
                        Repeater::make('stats')
                            ->label('Štatistiky')
                            ->table([
                                TableColumn::make('Číslo'),
                                TableColumn::make('Popis'),
                                TableColumn::make('Ikona'),
                            ])
                            ->schema([
                                TextInput::make('number')
                                    ->label('Číslo')
                                    ->required(),
                                TextInput::make('label')
                                    ->label('Popis')
                                    ->required(),
                                IconPicker::make('icon')
                                    ->label('Ikona')
                                    ->sets(['heroicons'])
                                    ->columns(3),
                            ])
                            ->defaultItems(0)
                            ->reorderable()
                            ->columnSpanFull(),
                    ])
                    ->collapsible(),
            ]);
    }
}
