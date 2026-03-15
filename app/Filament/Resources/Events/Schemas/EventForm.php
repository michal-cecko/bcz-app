<?php

namespace App\Filament\Resources\Events\Schemas;

use App\Enums\EventPricingTypeEnum;
use App\Enums\EventTypeEnum;
use App\Enums\RegistrationFieldTypeEnum;
use App\Mason\Bricks\CompetitionBracketsBrick;
use App\Mason\Bricks\CompetitionResultsBrick;
use App\Mason\Bricks\CompetitionTimetableBrick;
use App\Mason\Bricks\CtaBrick;
use App\Mason\Bricks\DividerBrick;
use App\Mason\Bricks\FeatureCardsBrick;
use App\Mason\Bricks\GalleryBrick;
use App\Mason\Bricks\HeadingBrick;
use App\Mason\Bricks\HeroBrick;
use App\Mason\Bricks\ImageBrick;
use App\Mason\Bricks\ImageTextBrick;
use App\Mason\Bricks\QuoteBrick;
use App\Mason\Bricks\RichTextBrick;
use App\Mason\Bricks\StatsBrick;
use App\Mason\Bricks\TableBrick;
use Awcodes\Mason\Mason;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Repeater\TableColumn;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Model;

class EventForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('Podujatie')
                    ->tabs([
                        Tabs\Tab::make('Základné')
                            ->icon('heroicon-o-document-text')
                            ->schema(self::baseTab()),
                        Tabs\Tab::make('Organizácia')
                            ->icon('heroicon-o-clipboard-document-list')
                            ->schema(self::organizationTab())
                            ->visible(fn (Get $get): bool => in_array($get('event_type'), [
                                EventTypeEnum::Organized->value,
                                EventTypeEnum::Competition->value,
                            ])),
                        Tabs\Tab::make('Súťaž')
                            ->icon('heroicon-o-trophy')
                            ->schema(self::competitionTab())
                            ->visible(fn (Get $get): bool => $get('event_type') === EventTypeEnum::Competition->value),
                    ])
                    ->columnSpanFull()
                    ->persistTabInQueryString(),
            ]);
    }

    private static function baseTab(): array
    {
        return [
            Grid::make(3)
                ->schema([
                    Section::make('Obsah')
                        ->schema([
                            Select::make('event_type')
                                ->label('Typ podujatia')
                                ->options(EventTypeEnum::class)
                                ->required()
                                ->default(EventTypeEnum::Report->value)
                                ->live(),
                            Tabs::make('Preklady')
                                ->tabs([
                                    Tabs\Tab::make('SK')
                                        ->schema([
                                            TextInput::make('title.sk')
                                                ->label('Názov (SK)')
                                                ->required(),
                                            Textarea::make('card_description.sk')
                                                ->label('Popis na karte (SK)')
                                                ->rows(2),
                                        ]),
                                    Tabs\Tab::make('EN')
                                        ->schema([
                                            TextInput::make('title.en')
                                                ->label('Názov (EN)'),
                                            Textarea::make('card_description.en')
                                                ->label('Popis na karte (EN)')
                                                ->rows(2),
                                        ]),
                                    Tabs\Tab::make('CZ')
                                        ->schema([
                                            TextInput::make('title.cs')
                                                ->label('Názov (CZ)'),
                                            Textarea::make('card_description.cs')
                                                ->label('Popis na karte (CZ)')
                                                ->rows(2),
                                        ]),
                                ])
                                ->columnSpanFull(),
                            TextInput::make('slug')
                                ->disabled()
                                ->dehydrated(),
                            Tabs::make('Obsah preklady')
                                ->tabs([
                                    Tabs\Tab::make('SK')
                                        ->schema([
                                            Mason::make('content.sk')
                                                ->label('Obsah (SK)')
                                                ->bricks(self::bricks())
                                                ->columnSpanFull(),
                                        ]),
                                    Tabs\Tab::make('EN')
                                        ->schema([
                                            Mason::make('content.en')
                                                ->label('Obsah (EN)')
                                                ->bricks(self::bricks())
                                                ->columnSpanFull(),
                                        ]),
                                    Tabs\Tab::make('CZ')
                                        ->schema([
                                            Mason::make('content.cs')
                                                ->label('Obsah (CZ)')
                                                ->bricks(self::bricks())
                                                ->columnSpanFull(),
                                        ]),
                                ])
                                ->columnSpanFull(),
                        ])
                        ->columnSpan(2),

                    Grid::make(1)
                        ->schema([
                            Section::make('Publikovanie')
                                ->schema([
                                    Toggle::make('is_published')
                                        ->label('Publikované')
                                        ->default(false),
                                    DateTimePicker::make('published_at')
                                        ->label('Dátum publikovania'),
                                    Select::make('event_category_id')
                                        ->label('Kategória')
                                        ->relationship(name: 'eventCategory')
                                        ->getOptionLabelFromRecordUsing(fn (Model $record): string => $record->getTranslation('title', 'sk'))
                                        ->required()
                                        ->preload()
                                        ->searchable(['title->sk']),
                                ]),

                            Section::make('Detaily')
                                ->schema([
                                    DatePicker::make('date')
                                        ->label('Dátum')
                                        ->required(),
                                    DatePicker::make('date_end')
                                        ->label('Dátum konca'),
                                    TextInput::make('country')
                                        ->label('Krajina'),
                                    TextInput::make('city')
                                        ->label('Mesto'),
                                    TextInput::make('place_name')
                                        ->label('Názov miesta'),
                                    TextInput::make('place_address')
                                        ->label('Adresa'),
                                    TextInput::make('latitude')
                                        ->label('Zemepisná šírka')
                                        ->numeric(),
                                    TextInput::make('longitude')
                                        ->label('Zemepisná dĺžka')
                                        ->numeric(),
                                    TextInput::make('attendee_count')
                                        ->label('Počet účastníkov')
                                        ->numeric()
                                        ->visible(fn (Get $get): bool => $get('event_type') === EventTypeEnum::Report->value),
                                    TextInput::make('client')
                                        ->label('Klient')
                                        ->visible(fn (Get $get): bool => $get('event_type') === EventTypeEnum::Report->value),
                                    SpatieMediaLibraryFileUpload::make('card_image')
                                        ->collection('card_image')
                                        ->disk('public')
                                        ->visibility('public')
                                        ->label('Obrázok na karte'),
                                    SpatieMediaLibraryFileUpload::make('detail_image')
                                        ->collection('detail_image')
                                        ->disk('public')
                                        ->visibility('public')
                                        ->label('Obrázok detailu'),
                                ]),
                        ])
                        ->columnSpan(1),
                ]),
        ];
    }

    private static function organizationTab(): array
    {
        return [
            Section::make('Registrácia a kapacita')
                ->relationship('organization')
                ->schema([
                    Grid::make(2)
                        ->schema([
                            TextInput::make('max_capacity')
                                ->label('Max. kapacita')
                                ->numeric()
                                ->minValue(1),
                            Select::make('pricing_type')
                                ->label('Typ ceny')
                                ->options(EventPricingTypeEnum::class)
                                ->default(EventPricingTypeEnum::Free->value)
                                ->required()
                                ->live(),
                            TextInput::make('price_amount')
                                ->label('Suma')
                                ->numeric()
                                ->minValue(0.01)
                                ->visible(fn (Get $get): bool => $get('pricing_type') === EventPricingTypeEnum::Paid->value),
                            Select::make('price_currency')
                                ->label('Mena')
                                ->options([
                                    'EUR' => 'EUR',
                                    'CZK' => 'CZK',
                                    'USD' => 'USD',
                                ])
                                ->default('EUR')
                                ->visible(fn (Get $get): bool => $get('pricing_type') === EventPricingTypeEnum::Paid->value),
                            DateTimePicker::make('registration_opens_at')
                                ->label('Registrácia od'),
                            DateTimePicker::make('registration_closes_at')
                                ->label('Registrácia do'),
                            Toggle::make('is_public_registration')
                                ->label('Verejná registrácia')
                                ->default(true),
                            Toggle::make('show_countdown')
                                ->label('Odpočítavanie'),
                            TextInput::make('external_link')
                                ->label('Externý odkaz')
                                ->url()
                                ->columnSpanFull(),
                        ]),
                ]),
            Section::make('Registračný formulár')
                ->relationship('organization')
                ->schema([
                    Repeater::make('registration_form_schema')
                        ->label('Polia formulára')
                        ->table([
                            TableColumn::make('Kľúč'),
                            TableColumn::make('Typ'),
                            TableColumn::make('Označenie'),
                            TableColumn::make('Povinné'),
                        ])
                        ->schema([
                            TextInput::make('key')
                                ->label('Kľúč')
                                ->required()
                                ->alphaNum(),
                            Select::make('type')
                                ->label('Typ')
                                ->options(RegistrationFieldTypeEnum::class)
                                ->required(),
                            TextInput::make('label')
                                ->label('Označenie')
                                ->required(),
                            Toggle::make('required')
                                ->label('Povinné')
                                ->default(false),
                            Textarea::make('options')
                                ->label('Možnosti (jedna na riadok)')
                                ->rows(3)
                                ->helperText('Pre select/multi_select')
                                ->visible(fn (Get $get): bool => in_array($get('type'), [
                                    RegistrationFieldTypeEnum::Select->value,
                                    RegistrationFieldTypeEnum::MultiSelect->value,
                                ])),
                        ])
                        ->defaultItems(0)
                        ->columnSpanFull(),
                ]),
        ];
    }

    private static function competitionTab(): array
    {
        return [
            Section::make('Kategórie a disciplíny')
                ->relationship('competitionDetail')
                ->schema([
                    Select::make('athleteCategories')
                        ->label('Kategórie atlétov')
                        ->relationship('athleteCategories')
                        ->getOptionLabelFromRecordUsing(fn (Model $record): string => $record->getTranslation('name', 'sk'))
                        ->multiple()
                        ->preload()
                        ->searchable(),
                    Select::make('disciplines')
                        ->label('Disciplíny')
                        ->relationship('disciplines')
                        ->getOptionLabelFromRecordUsing(fn (Model $record): string => $record->getTranslation('name', 'sk'))
                        ->multiple()
                        ->preload()
                        ->searchable(),
                ]),
        ];
    }

    /** @return list<class-string<\Awcodes\Mason\Brick>> */
    private static function bricks(): array
    {
        return [
            HeroBrick::class,
            RichTextBrick::class,
            ImageBrick::class,
            ImageTextBrick::class,
            FeatureCardsBrick::class,
            CtaBrick::class,
            GalleryBrick::class,
            DividerBrick::class,
            QuoteBrick::class,
            HeadingBrick::class,
            StatsBrick::class,
            TableBrick::class,
            CompetitionResultsBrick::class,
            CompetitionBracketsBrick::class,
            CompetitionTimetableBrick::class,
        ];
    }
}
