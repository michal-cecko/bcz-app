<?php

namespace App\Filament\Resources\Trainings\Schemas;

use App\Enums\GenderEnum;
use App\Enums\RegistrationFieldTypeEnum;
use App\Enums\TrainingPricingTypeEnum;
use Cheesegrits\FilamentGoogleMaps\Fields\Map;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class TrainingForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('Tréning')
                    ->tabs([
                        Tabs\Tab::make('Základné')
                            ->columns(2)
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

                        Tabs\Tab::make('Miesto')
                            ->columns(4)
                            ->schema([
                                Tabs::make('Preklady miesta')
                                    ->tabs([
                                        Tabs\Tab::make('SK')
                                            ->columns(2)
                                            ->schema([
                                                TextInput::make('place_name.sk')
                                                    ->label('Názov miesta (SK)'),
                                                TextInput::make('gathering_place.sk')
                                                    ->label('Miesto stretnutia (SK)'),
                                            ]),
                                        Tabs\Tab::make('EN')
                                            ->columns(2)
                                            ->schema([
                                                TextInput::make('place_name.en')
                                                    ->label('Názov miesta (EN)'),
                                                TextInput::make('gathering_place.en')
                                                    ->label('Miesto stretnutia (EN)'),
                                            ]),
                                    ])
                                    ->columnSpanFull(),
                                TextInput::make('place_address')
                                    ->label('Adresa')
                                    ->columnSpan(2),
                                TextInput::make('latitude')
                                    ->label('Zemepisná šírka')
                                    ->disabled()
                                    ->dehydrated(),
                                TextInput::make('longitude')
                                    ->label('Zemepisná dĺžka')
                                    ->disabled()
                                    ->dehydrated(),
                                Map::make('location')
                                    ->label('Mapa')
                                    ->defaultLocation([48.1486, 17.1077])
                                    ->defaultZoom(12)
                                    ->draggable()
                                    ->clickable()
                                    ->autocomplete('place_address')
                                    ->reverseGeocode([
                                        'city' => '%L',
                                        'country' => '%C',
                                    ])
                                    ->columnSpanFull(),
                            ]),

                        Tabs\Tab::make('Rozvrh a kapacita')
                            ->columns(2)
                            ->schema([
                                Section::make('Rozvrh')
                                    ->schema([
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
                            ]),

                        Tabs\Tab::make('Registrácia')
                            ->schema([
                                Section::make('Registračný formulár')
                                    ->description('Predvolené polia: Meno, Priezvisko, Email. Ďalšie polia definujte nižšie.')
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

                        Tabs\Tab::make('Nastavenia')
                            ->columns(4)
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
                                    ->inline(false)
                                    ->default(true),
                                TextInput::make('sort_order')
                                    ->label('Poradie')
                                    ->numeric()
                                    ->default(0),
                            ]),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
