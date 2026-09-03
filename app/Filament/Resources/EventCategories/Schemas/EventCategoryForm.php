<?php

namespace App\Filament\Resources\EventCategories\Schemas;

use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Repeater\TableColumn;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Schema;
use Guava\IconPicker\Forms\Components\IconPicker;

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
                                        TextInput::make('title.cs')
                                            ->label('Názov (CZ)'),
                                        TextInput::make('card_subtitle.cs')
                                            ->label('Podtitulok karty (CZ)'),
                                        Textarea::make('card_description.cs')
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
                            ->options(self::colorOptions())
                            ->searchable()
                            ->allowHtml()
                            ->native(false),
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
                        SpatieMediaLibraryFileUpload::make('card_image')
                            ->collection('card_image')
                            ->disk('public')
                            ->visibility('public')
                            ->maxSize(10240)
                            ->label('Obrázok karty'),
                        SpatieMediaLibraryFileUpload::make('detail_image')
                            ->collection('detail_image')
                            ->disk('public')
                            ->visibility('public')
                            ->maxSize(10240)
                            ->label('Obrázok detailu'),
                        SpatieMediaLibraryFileUpload::make('hero_image')
                            ->collection('hero_image')
                            ->disk('public')
                            ->visibility('public')
                            ->maxSize(10240)
                            ->label('Hero obrázok'),
                        SpatieMediaLibraryFileUpload::make('about_image')
                            ->collection('about_image')
                            ->disk('public')
                            ->visibility('public')
                            ->maxSize(10240)
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
                                        TextInput::make('detail_title.cs')
                                            ->label('Titulok detailu (CZ)'),
                                        TextInput::make('about_title.cs')
                                            ->label('Titulok O nás (CZ)'),
                                        Textarea::make('about_description.cs')
                                            ->label('Popis O nás (CZ)')
                                            ->rows(3),
                                        TextInput::make('types_section_title.cs')
                                            ->label('Titulok sekcie typov (CZ)'),
                                        TextInput::make('types_section_subtitle.cs')
                                            ->label('Podtitulok sekcie typov (CZ)'),
                                        TextInput::make('cta_title.cs')
                                            ->label('CTA titulok (CZ)'),
                                        Textarea::make('cta_description.cs')
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
                            ->reorderableWithButtons()
                            ->cloneable()
                            ->collapsible()
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
                            ->reorderableWithButtons()
                            ->cloneable()
                            ->collapsible()
                            ->columnSpanFull(),
                    ])
                    ->collapsible(),
            ]);
    }

    private static function colorOptions(): array
    {
        $colors = [
            '#FF6B35' => 'Oranžová (Vystúpenia)',
            '#2EC4B6' => 'Teal (Prednášky)',
            '#9B5DE5' => 'Fialová (Workshopy)',
            '#FF6B6B' => 'Červená (Freestyle)',
            '#FF2D2D' => 'Červená (BCZ)',
            '#3B82F6' => 'Modrá',
            '#22C55E' => 'Zelená',
            '#F59E0B' => 'Žltá',
            '#EC4899' => 'Ružová',
            '#6B7280' => 'Sivá',
            '#14B8A6' => 'Tyrkysová',
            '#8B5CF6' => 'Fialová',
        ];

        $options = [];

        foreach ($colors as $hex => $label) {
            $options[$hex] = '<span style="display:inline-flex;align-items:center;gap:8px;"><span style="width:14px;height:14px;border-radius:3px;background:'.$hex.';display:inline-block;"></span> '.$label.'</span>';
        }

        return $options;
    }
}
