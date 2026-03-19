<?php

namespace App\Filament\Resources\Banners\Schemas;

use App\Enums\BannerTypeEnum;
use App\Mason\Support\LinkPickerField;
use App\Models\Page;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Guava\IconPicker\Forms\Components\IconPicker;

class BannerForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make(3)
                    ->columnSpanFull()
                    ->schema([
                        Section::make('Obsah')
                            ->schema([
                                Tabs::make('Preklady')
                                    ->tabs([
                                        Tabs\Tab::make('SK')
                                            ->schema([
                                                TextInput::make('name.sk')
                                                    ->label('Nazov (SK)')
                                                    ->required(),
                                                ...self::translatableContentFields('sk'),
                                            ]),
                                        Tabs\Tab::make('EN')
                                            ->schema([
                                                TextInput::make('name.en')
                                                    ->label('Nazov (EN)'),
                                                ...self::translatableContentFields('en'),
                                            ]),
                                        Tabs\Tab::make('CZ')
                                            ->schema([
                                                TextInput::make('name.cs')
                                                    ->label('Nazov (CZ)'),
                                                ...self::translatableContentFields('cs'),
                                            ]),
                                    ])
                                    ->columnSpanFull(),

                                Section::make('Vzhľad')
                                    ->schema([
                                        IconPicker::make('content.icon')
                                            ->label('Ikona')
                                            ->sets(['lucide'])
                                            ->columns(3),
                                        Select::make('content.bg_color')
                                            ->label('Farba pozadia')
                                            ->options(self::colorOptions())
                                            ->searchable()
                                            ->allowHtml()
                                            ->native(false),
                                    ])
                                    ->columns(2)
                                    ->columnSpanFull(),

                                Section::make('Štatistiky')
                                    ->schema([
                                        TextInput::make('content.stat1_value')
                                            ->label('Štatistika 1 — hodnota')
                                            ->placeholder('500+'),
                                        TextInput::make('content.stat2_value')
                                            ->label('Štatistika 2 — hodnota')
                                            ->placeholder('10+'),
                                    ])
                                    ->columns(2)
                                    ->visible(fn (Get $get): bool => $get('type') === BannerTypeEnum::Floating->value)
                                    ->columnSpanFull(),

                                Section::make('Promo obrazok')
                                    ->schema([
                                        FileUpload::make('content.image')
                                            ->label('Obrazok')
                                            ->image()
                                            ->directory('banners')
                                            ->visibility('public')
                                            ->columnSpanFull(),
                                    ])
                                    ->visible(fn (Get $get): bool => $get('type') === BannerTypeEnum::Popup->value)
                                    ->columnSpanFull(),
                            ])
                            ->columnSpan(2),

                        Grid::make(1)
                            ->schema([
                                Section::make('Nastavenia')
                                    ->schema([
                                        Select::make('type')
                                            ->label('Typ')
                                            ->options(BannerTypeEnum::class)
                                            ->required()
                                            ->live(),
                                        Select::make('placement')
                                            ->label('Umiestnenie')
                                            ->options([
                                                'all' => 'Všetky stránky',
                                                'specific' => 'Konkrétna stránka',
                                            ])
                                            ->default('all')
                                            ->required()
                                            ->live(),
                                        Select::make('page_ids')
                                            ->label('Stránky')
                                            ->options(fn () => Page::query()->orderBy('sort_order')->get()->mapWithKeys(
                                                fn (Page $page) => [$page->id => $page->getTranslation('title', 'sk')]
                                            ))
                                            ->multiple()
                                            ->searchable()
                                            ->visible(fn (Get $get): bool => $get('placement') === 'specific'),
                                        Toggle::make('is_active')
                                            ->label('Aktívny'),
                                        DateTimePicker::make('active_from')
                                            ->label('Aktívny od'),
                                        DateTimePicker::make('active_to')
                                            ->label('Aktívny do'),
                                        TextInput::make('sort_order')
                                            ->label('Poradie')
                                            ->numeric()
                                            ->default(0),
                                    ]),
                            ])
                            ->columnSpan(1),
                    ]),
            ]);
    }

    /** @return list<Component> */
    private static function translatableContentFields(string $locale): array
    {
        $suffix = ' ('.strtoupper($locale).')';

        return [
            TextInput::make("content.title.{$locale}")
                ->label("Nadpis{$suffix}"),
            Textarea::make("content.description.{$locale}")
                ->label("Popis{$suffix}")
                ->rows(2)
                ->visible(fn (Get $get): bool => $get('../../type') !== BannerTypeEnum::Topbar->value),
            LinkPickerField::make(
                'content.primary_button_',
                $locale,
                "Primárne tlačidlo{$suffix}",
                'content.primary_button_text',
                "Text tlačidla{$suffix}",
            ),
            TextInput::make("content.stat1_label.{$locale}")
                ->label("Štatistika 1 — popis{$suffix}")
                ->visible(fn (Get $get): bool => $get('../../type') === BannerTypeEnum::Floating->value),
            TextInput::make("content.stat2_label.{$locale}")
                ->label("Štatistika 2 — popis{$suffix}")
                ->visible(fn (Get $get): bool => $get('../../type') === BannerTypeEnum::Floating->value),
            LinkPickerField::make(
                'content.secondary_button_',
                $locale,
                "Sekundárne tlačidlo{$suffix}",
                'content.secondary_button_text',
                "Text tlačidla{$suffix}",
            )
                ->visible(fn (Get $get): bool => $get('../../type') === BannerTypeEnum::Popup->value),
            TextInput::make("content.badge_text.{$locale}")
                ->label("Odznak{$suffix}")
                ->visible(fn (Get $get): bool => $get('../../type') === BannerTypeEnum::Popup->value),
            TextInput::make("content.note.{$locale}")
                ->label("Poznámka{$suffix}")
                ->visible(fn (Get $get): bool => in_array($get('../../type'), [BannerTypeEnum::Floating->value, BannerTypeEnum::Popup->value])),
        ];
    }

    /** @return array<string, string> */
    private static function colorOptions(): array
    {
        $colors = [
            '#FF2D2D' => 'Červená (BCZ)',
            '#1A1A1A' => 'Tmavá',
            '#111111' => 'Čierna',
            '#FFFFFF' => 'Biela',
            '#3B82F6' => 'Modrá',
            '#22C55E' => 'Zelená',
            '#F59E0B' => 'Žltá',
            '#9333EA' => 'Fialová',
            '#EC4899' => 'Ružová',
        ];

        $options = [];

        foreach ($colors as $hex => $label) {
            $border = $hex === '#FFFFFF' ? 'border:1px solid #ddd;' : '';
            $options[$hex] = '<span style="display:inline-flex;align-items:center;gap:8px;"><span style="width:14px;height:14px;border-radius:3px;background:'.$hex.';display:inline-block;'.$border.'"></span> '.$label.'</span>';
        }

        return $options;
    }
}
