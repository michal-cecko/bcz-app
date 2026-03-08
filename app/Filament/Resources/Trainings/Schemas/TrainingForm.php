<?php

namespace App\Filament\Resources\Trainings\Schemas;

use App\Enums\GenderEnum;
use App\Enums\RegistrationFieldTypeEnum;
use App\Enums\TrainingPricingTypeEnum;
use Cheesegrits\FilamentGoogleMaps\Fields\Map;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Repeater\TableColumn;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Model;

class TrainingForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make(3)
                    ->columnSpanFull()
                    ->schema([
                        Grid::make(1)
                            ->schema([
                                Section::make('Základné údaje')
                                    ->schema([
                                        Tabs::make('Preklady')
                                            ->tabs([
                                                Tabs\Tab::make('SK')
                                                    ->schema([
                                                        TextInput::make('title.sk')
                                                            ->label('Názov (SK)')
                                                            ->required(),
                                                        Textarea::make('description.sk')
                                                            ->label('Popis (SK)')
                                                            ->rows(3),
                                                    ]),
                                                Tabs\Tab::make('EN')
                                                    ->schema([
                                                        TextInput::make('title.en')
                                                            ->label('Názov (EN)'),
                                                        Textarea::make('description.en')
                                                            ->label('Popis (EN)')
                                                            ->rows(3),
                                                    ]),
                                                Tabs\Tab::make('CZ')
                                                    ->schema([
                                                        TextInput::make('title.cz')
                                                            ->label('Názov (CZ)'),
                                                        Textarea::make('description.cz')
                                                            ->label('Popis (CZ)')
                                                            ->rows(3),
                                                    ]),
                                            ])
                                            ->columnSpanFull(),
                                        TextInput::make('slug')
                                            ->disabled()
                                            ->dehydrated(),
                                        Select::make('sport_category_id')
                                            ->label('Športová kategória')
                                            ->relationship(name: 'sportCategory', titleAttribute: 'name')
                                            ->getOptionLabelFromRecordUsing(fn (Model $record): string => $record->getTranslation('name', 'sk'))
                                            ->required()
                                            ->preload()
                                            ->searchable(),
                                    ]),

                                Section::make('Miesto konania')
                                    ->schema([
                                        Tabs::make('Názov miesta')
                                            ->tabs([
                                                Tabs\Tab::make('SK')
                                                    ->schema([
                                                        TextInput::make('place_name.sk')
                                                            ->label('Názov miesta (SK)'),
                                                    ]),
                                                Tabs\Tab::make('EN')
                                                    ->schema([
                                                        TextInput::make('place_name.en')
                                                            ->label('Názov miesta (EN)'),
                                                    ]),
                                            ])
                                            ->columnSpanFull(),
                                        TextInput::make('place_address')
                                            ->label('Adresa'),
                                        Map::make('location')
                                            ->label('Mapa')
                                            ->defaultLocation([48.1486, 17.1077])
                                            ->defaultZoom(12)
                                            ->draggable()
                                            ->clickable()
                                            ->columnSpanFull(),
                                        TextInput::make('latitude')
                                            ->label('Zemepisná šírka')
                                            ->numeric(),
                                        TextInput::make('longitude')
                                            ->label('Zemepisná dĺžka')
                                            ->numeric(),
                                        Tabs::make('Miesto stretnutia')
                                            ->tabs([
                                                Tabs\Tab::make('SK')
                                                    ->schema([
                                                        TextInput::make('gathering_place.sk')
                                                            ->label('Miesto stretnutia (SK)'),
                                                    ]),
                                                Tabs\Tab::make('EN')
                                                    ->schema([
                                                        TextInput::make('gathering_place.en')
                                                            ->label('Miesto stretnutia (EN)'),
                                                    ]),
                                            ])
                                            ->columnSpanFull(),
                                    ]),

                                Section::make('Registračný formulár')
                                    ->description('Predvolené polia: Meno, Priezvisko, Email. Ďalšie polia definujte nižšie.')
                                    ->schema([
                                        Repeater::make('registration_form_schema')
                                            ->label('Vlastné polia')
                                            ->table([
                                                TableColumn::make('Label'),
                                                TableColumn::make('Name'),
                                                TableColumn::make('Type'),
                                                TableColumn::make('Required'),
                                                TableColumn::make('Width'),
                                                TableColumn::make('Placeholder'),
                                                TableColumn::make('Options'),
                                            ])
                                            ->schema([
                                                TextInput::make('label')
                                                    ->required(),
                                                TextInput::make('name')
                                                    ->required(),
                                                Select::make('type')
                                                    ->options(RegistrationFieldTypeEnum::class)
                                                    ->required()
                                                    ->default(RegistrationFieldTypeEnum::TEXT_INPUT),
                                                Toggle::make('required')
                                                    ->default(false),
                                                Select::make('width')
                                                    ->options([
                                                        'half' => 'Polovica',
                                                        'full' => 'Celý riadok',
                                                    ])
                                                    ->default('half'),
                                                TextInput::make('placeholder'),
                                                TextInput::make('options')
                                                    ->placeholder('Čiarkou oddelené (pre Select/MultiSelect)'),
                                            ])
                                            ->defaultItems(0)
                                            ->reorderable()
                                            ->columnSpanFull(),
                                    ])
                                    ->collapsible(),
                            ])
                            ->columnSpan(2),

                        Grid::make(1)
                            ->schema([
                                Section::make('Rozvrh')
                                    ->schema([
                                        TextInput::make('frequency')
                                            ->label('Frekvencia')
                                            ->placeholder('napr. 2x týždenne'),
                                        TextInput::make('duration_minutes')
                                            ->label('Trvanie')
                                            ->numeric()
                                            ->suffix('minút'),
                                        TimePicker::make('start_time')
                                            ->label('Čas začiatku'),
                                        CheckboxList::make('schedule_days')
                                            ->label('Dni v týždni')
                                            ->options([
                                                'monday' => 'Pondelok',
                                                'tuesday' => 'Utorok',
                                                'wednesday' => 'Streda',
                                                'thursday' => 'Štvrtok',
                                                'friday' => 'Piatok',
                                                'saturday' => 'Sobota',
                                                'sunday' => 'Nedeľa',
                                            ])
                                            ->columns(2),
                                    ]),

                                Section::make('Kapacita a ceny')
                                    ->schema([
                                        TextInput::make('max_capacity')
                                            ->label('Max. kapacita')
                                            ->numeric(),
                                        Toggle::make('notify_on_available')
                                            ->label('Upozorniť pri voľnom mieste'),
                                        Select::make('pricing_type')
                                            ->label('Typ ceny')
                                            ->options(TrainingPricingTypeEnum::class)
                                            ->required()
                                            ->default(TrainingPricingTypeEnum::FREE)
                                            ->live(),
                                        TextInput::make('price_amount')
                                            ->label('Cena')
                                            ->numeric()
                                            ->prefix('€')
                                            ->visible(fn (Get $get): bool => $get('pricing_type') === TrainingPricingTypeEnum::PAID->value),
                                    ]),

                                Section::make('Nastavenia')
                                    ->schema([
                                        TextInput::make('age_group')
                                            ->label('Veková skupina')
                                            ->placeholder('napr. 6-10, 14-18, 18+'),
                                        Select::make('gender')
                                            ->label('Pohlavie')
                                            ->options(GenderEnum::class)
                                            ->placeholder('Všetky pohlavia'),
                                        Toggle::make('is_active')
                                            ->label('Aktívny')
                                            ->default(true),
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
}
