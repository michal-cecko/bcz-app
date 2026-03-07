<?php

namespace App\Filament\Resources\Competitions\Schemas;

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
use App\Models\AthleteCategory;
use App\Models\Discipline;
use Awcodes\Mason\Mason;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Repeater\TableColumn;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Model;

class CompetitionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('Súťaž')
                    ->tabs([
                        Tabs\Tab::make('Základné')
                            ->schema([
                                Tabs::make('Preklady názvu')
                                    ->tabs([
                                        Tabs\Tab::make('SK')
                                            ->schema([
                                                TextInput::make('name.sk')
                                                    ->label('Názov (SK)')
                                                    ->required(),
                                            ]),
                                        Tabs\Tab::make('EN')
                                            ->schema([
                                                TextInput::make('name.en')
                                                    ->label('Názov (EN)'),
                                            ]),
                                        Tabs\Tab::make('CZ')
                                            ->schema([
                                                TextInput::make('name.cz')
                                                    ->label('Názov (CZ)'),
                                            ]),
                                    ])
                                    ->columnSpanFull(),
                                TextInput::make('slug')
                                    ->disabled()
                                    ->dehydrated(),
                                Mason::make('description')
                                    ->label('Popis')
                                    ->bricks([
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
                                    ])
                                    ->columnSpanFull(),

                                Section::make('Miesto konania')
                                    ->schema([
                                        TextInput::make('place_name')
                                            ->label('Názov miesta'),
                                        TextInput::make('place_address')
                                            ->label('Adresa'),
                                        TextInput::make('country')
                                            ->label('Krajina'),
                                        TextInput::make('city')
                                            ->label('Mesto'),
                                        TextInput::make('latitude')
                                            ->label('Zemepisná šírka')
                                            ->numeric(),
                                        TextInput::make('longitude')
                                            ->label('Zemepisná dĺžka')
                                            ->numeric(),
                                    ])
                                    ->columns(2),

                                Section::make('Dátumy')
                                    ->schema([
                                        DatePicker::make('date_start')
                                            ->label('Začiatok')
                                            ->required(),
                                        DatePicker::make('date_end')
                                            ->label('Koniec'),
                                    ])
                                    ->columns(2),

                                Section::make('Publikovanie')
                                    ->schema([
                                        Select::make('organizer_team_id')
                                            ->label('Organizátor')
                                            ->relationship(name: 'organizerTeam')
                                            ->getOptionLabelFromRecordUsing(fn (Model $record): string => $record->getTranslation('name', 'sk'))
                                            ->preload()
                                            ->searchable(['name->sk']),
                                        TextInput::make('external_link')
                                            ->label('Externý odkaz')
                                            ->url()
                                            ->placeholder('Pre externé súťaže'),
                                        Toggle::make('is_published')
                                            ->label('Publikované')
                                            ->default(false),
                                        DateTimePicker::make('published_at')
                                            ->label('Dátum publikovania'),
                                    ])
                                    ->columns(2),
                            ]),

                        Tabs\Tab::make('Registrácia')
                            ->schema([
                                DateTimePicker::make('registration_opens_at')
                                    ->label('Otvorenie registrácie'),
                                DateTimePicker::make('registration_closes_at')
                                    ->label('Uzavretie registrácie'),
                                Toggle::make('is_public_registration')
                                    ->label('Verejná registrácia')
                                    ->helperText('Ak je vypnuté, registrovať sa môžu len pozvaní/prihlásení používatelia')
                                    ->default(true),
                                Toggle::make('show_countdown')
                                    ->label('Zobraziť odpočet')
                                    ->default(false),

                                Section::make('Registračné poplatky')
                                    ->schema([
                                        Repeater::make('registrationFees')
                                            ->label('Poplatky')
                                            ->relationship()
                                            ->table([
                                                TableColumn::make('Kategória'),
                                                TableColumn::make('Suma'),
                                                TableColumn::make('Mena'),
                                                TableColumn::make('Popis'),
                                            ])
                                            ->schema([
                                                Select::make('athlete_category_id')
                                                    ->label('Kategória')
                                                    ->relationship(name: 'athleteCategory')
                                                    ->getOptionLabelFromRecordUsing(fn (Model $record): string => $record->getTranslation('name', 'sk'))
                                                    ->placeholder('Všetky kategórie')
                                                    ->preload()
                                                    ->searchable(['name->sk']),
                                                TextInput::make('amount')
                                                    ->label('Suma')
                                                    ->numeric()
                                                    ->required()
                                                    ->prefix('€'),
                                                Select::make('currency')
                                                    ->label('Mena')
                                                    ->options([
                                                        'EUR' => 'EUR',
                                                        'USD' => 'USD',
                                                        'CZK' => 'CZK',
                                                    ])
                                                    ->default('EUR'),
                                                TextInput::make('description')
                                                    ->label('Popis'),
                                            ])
                                            ->defaultItems(0)
                                            ->reorderable(false)
                                            ->columnSpanFull(),
                                    ]),
                            ]),

                        Tabs\Tab::make('Kategórie')
                            ->schema([
                                CheckboxList::make('athleteCategories')
                                    ->label('Kategórie športovcov')
                                    ->relationship('athleteCategories')
                                    ->options(fn () => AthleteCategory::all()->mapWithKeys(fn (AthleteCategory $record) => [$record->id => $record->getTranslation('name', 'sk')]))
                                    ->columns(2),
                            ]),

                        Tabs\Tab::make('Disciplíny a rozhodcovia')
                            ->schema([
                                CheckboxList::make('disciplines')
                                    ->label('Disciplíny')
                                    ->relationship('disciplines')
                                    ->options(fn () => Discipline::all()->mapWithKeys(fn (Discipline $record) => [$record->id => $record->getTranslation('name', 'sk')]))
                                    ->columns(2),
                            ]),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
