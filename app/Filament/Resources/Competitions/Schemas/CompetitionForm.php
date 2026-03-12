<?php

namespace App\Filament\Resources\Competitions\Schemas;

use App\Enums\RegistrationFieldTypeEnum;
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
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

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
                                                TextInput::make('name.cs')
                                                    ->label('Názov (CZ)'),
                                            ]),
                                    ])
                                    ->columnSpanFull(),
                                TextInput::make('slug')
                                    ->disabled()
                                    ->dehydrated(),
                                Tabs::make('Popis preklady')
                                    ->tabs([
                                        Tabs\Tab::make('SK')
                                            ->schema([
                                                Mason::make('description.sk')
                                                    ->label('Popis (SK)')
                                                    ->bricks(self::bricks())
                                                    ->columnSpanFull(),
                                            ]),
                                        Tabs\Tab::make('EN')
                                            ->schema([
                                                Mason::make('description.en')
                                                    ->label('Popis (EN)')
                                                    ->bricks(self::bricks())
                                                    ->columnSpanFull(),
                                            ]),
                                        Tabs\Tab::make('CZ')
                                            ->schema([
                                                Mason::make('description.cs')
                                                    ->label('Popis (CZ)')
                                                    ->bricks(self::bricks())
                                                    ->columnSpanFull(),
                                            ]),
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
                            ->columns(2)
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

                                Section::make('Registračný formulár')
                                    ->description('Predvolené polia: Meno, Priezvisko, Email, Kategória. Ďalšie polia definujte nižšie.')
                                    ->schema([
                                        Repeater::make('registration_form_schema')
                                            ->label('Vlastné polia')
                                            ->itemLabel(fn (array $state): ?string => $state['label'] ?? null)
                                            ->columns(2)
                                            ->schema([
                                                TextInput::make('label')
                                                    ->label('Názov poľa')
                                                    ->required()
                                                    ->live(onBlur: true)
                                                    ->afterStateUpdated(fn (Set $set, ?string $state) => $set('name', Str::slug($state ?? '', '_'))),
                                                TextInput::make('name')
                                                    ->label('Kľúč')
                                                    ->required()
                                                    ->disabled()
                                                    ->dehydrated()
                                                    ->live(),
                                                Select::make('type')
                                                    ->label('Typ poľa')
                                                    ->options(RegistrationFieldTypeEnum::class)
                                                    ->required()
                                                    ->default(RegistrationFieldTypeEnum::TEXT_INPUT)
                                                    ->live(),
                                                Select::make('width')
                                                    ->label('Šírka')
                                                    ->options([
                                                        'half' => 'Polovica',
                                                        'full' => 'Celý riadok',
                                                    ])
                                                    ->default('half'),
                                                TextInput::make('placeholder')
                                                    ->label('Placeholder')
                                                    ->hidden(fn (Get $get): bool => in_array($get('type'), [
                                                        RegistrationFieldTypeEnum::SELECT->value,
                                                        RegistrationFieldTypeEnum::MULTI_SELECT->value,
                                                        RegistrationFieldTypeEnum::DATE_PICKER->value,
                                                        RegistrationFieldTypeEnum::YEAR_PICKER->value,
                                                        RegistrationFieldTypeEnum::TIME_PICKER->value,
                                                        RegistrationFieldTypeEnum::FILE_INPUT->value,
                                                    ])),
                                                TextInput::make('options')
                                                    ->label('Možnosti')
                                                    ->placeholder('Čiarkou oddelené')
                                                    ->required(fn (Get $get): bool => in_array($get('type'), [
                                                        RegistrationFieldTypeEnum::SELECT->value,
                                                        RegistrationFieldTypeEnum::MULTI_SELECT->value,
                                                    ]))
                                                    ->hidden(fn (Get $get): bool => ! in_array($get('type'), [
                                                        RegistrationFieldTypeEnum::SELECT->value,
                                                        RegistrationFieldTypeEnum::MULTI_SELECT->value,
                                                    ])),
                                                Toggle::make('required')
                                                    ->label('Povinné')
                                                    ->default(false),
                                                Section::make('Podmienka zobrazenia')
                                                    ->schema([
                                                        Toggle::make('has_condition')
                                                            ->label('Podmienené zobrazenie')
                                                            ->helperText('Zobraziť toto pole len ak iné pole má konkrétnu hodnotu')
                                                            ->default(false)
                                                            ->live(),
                                                        Select::make('condition_field')
                                                            ->label('Pole')
                                                            ->helperText('Pole, od ktorého závisí zobrazenie')
                                                            ->options(function (Get $get): array {
                                                                $items = $get('../../');
                                                                if (! is_array($items)) {
                                                                    return [];
                                                                }
                                                                $options = [];
                                                                foreach ($items as $item) {
                                                                    if (! empty($item['name']) && ! empty($item['label'])) {
                                                                        $options[$item['name']] = $item['label'];
                                                                    }
                                                                }

                                                                return $options;
                                                            })
                                                            ->required(fn (Get $get): bool => (bool) $get('has_condition'))
                                                            ->hidden(fn (Get $get): bool => ! $get('has_condition')),
                                                        TextInput::make('condition_value')
                                                            ->label('Očakávaná hodnota')
                                                            ->placeholder('napr. áno')
                                                            ->helperText('Pole sa zobrazí len ak referenčné pole má túto hodnotu')
                                                            ->required(fn (Get $get): bool => (bool) $get('has_condition'))
                                                            ->hidden(fn (Get $get): bool => ! $get('has_condition')),
                                                    ])
                                                    ->collapsible()
                                                    ->collapsed()
                                                    ->columnSpanFull(),
                                            ])
                                            ->addActionLabel('Pridať pole')
                                            ->deleteAction(fn ($action) => $action->requiresConfirmation())
                                            ->defaultItems(0)
                                            ->reorderable()
                                            ->reorderableWithButtons()
                                            ->cloneable()
                                            ->collapsible()
                                            ->columnSpanFull(),
                                    ])
                                    ->collapsible(),
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
        ];
    }
}
