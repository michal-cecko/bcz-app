<?php

namespace App\Filament\Resources\Trainings\Schemas;

use App\Enums\GenderEnum;
use App\Enums\RegistrationFieldTypeEnum;
use App\Enums\RegistrationStatusEnum;
use App\Enums\TrainingPricingTypeEnum;
use App\Mason\EmailBricks\EmailButtonBrick;
use App\Mason\EmailBricks\EmailCalloutBrick;
use App\Mason\EmailBricks\EmailDividerBrick;
use App\Mason\EmailBricks\EmailHeadingBrick;
use App\Mason\EmailBricks\EmailImageBrick;
use App\Mason\EmailBricks\EmailRichTextBrick;
use App\Mason\EmailBricks\EmailSpacerBrick;
use App\Models\TeamSeason;
use App\Support\RegistrationFieldOptions;
use Awcodes\Mason\Mason;
use Cheesegrits\FilamentGoogleMaps\Fields\Map;
use Filament\Facades\Filament;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Repeater\TableColumn;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TagsInput;
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
    /** @return list<class-string> */
    private static function emailBricks(): array
    {
        return [
            EmailRichTextBrick::class,
            EmailButtonBrick::class,
            EmailHeadingBrick::class,
            EmailImageBrick::class,
            EmailCalloutBrick::class,
            EmailDividerBrick::class,
            EmailSpacerBrick::class,
        ];
    }

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
                                                TextInput::make('title.cs')
                                                    ->label('Názov (CZ)'),
                                                Textarea::make('description.cs')
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
                                Select::make('city_id')
                                    ->label('Mesto')
                                    ->relationship(name: 'city', titleAttribute: 'name')
                                    ->getOptionLabelFromRecordUsing(fn (Model $record): string => $record->getTranslation('name', 'sk'))
                                    ->required()
                                    ->preload()
                                    ->searchable()
                                    ->createOptionForm([
                                        TextInput::make('name.sk')
                                            ->label('Názov (SK)')
                                            ->required(),
                                        TextInput::make('name.en')
                                            ->label('Názov (EN)'),
                                    ])
                                    ->columnSpanFull(),
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
                                    ->autocompleteReverse(true)
                                    ->reverseGeocode([
                                        'city' => '%L',
                                        'country' => '%C',
                                        'street' => '%n %S',
                                    ])
                                    ->reactive()
                                    ->afterStateUpdated(function (Get $get, Set $set, ?array $state): void {
                                        if ($state) {
                                            $set('latitude', $state['lat'] ?? null);
                                            $set('longitude', $state['lng'] ?? null);
                                        }
                                    })
                                    ->columnSpanFull(),
                            ]),

                        Tabs\Tab::make('Rozvrh a kapacita')
                            ->columns(2)
                            ->schema([
                                Section::make('Rozvrh')
                                    ->schema([
                                        Toggle::make('is_recurring')
                                            ->label('Pravidelný tréning')
                                            ->helperText('Pravidelný = opakuje sa každý týždeň. Jednorazový = konkrétny dátum.')
                                            ->default(true)
                                            ->live()
                                            ->afterStateUpdated(function (Get $get, Set $set, bool $state): void {
                                                if ($state) {
                                                    $set('event_date', null);
                                                    if ($get('pricing_type') === TrainingPricingTypeEnum::PAID->value) {
                                                        $set('pricing_type', TrainingPricingTypeEnum::FREE->value);
                                                        $set('price_amount', null);
                                                    }
                                                } else {
                                                    $set('schedules', []);
                                                    if ($get('pricing_type') === TrainingPricingTypeEnum::MEMBERSHIP_REQUIRED->value) {
                                                        $set('pricing_type', TrainingPricingTypeEnum::FREE->value);
                                                    }
                                                }
                                            }),
                                        TextInput::make('duration_minutes')
                                            ->label('Trvanie')
                                            ->numeric()
                                            ->suffix('minút'),
                                        Repeater::make('schedules')
                                            ->relationship()
                                            ->label('Rozvrh')
                                            ->schema([
                                                Select::make('day')
                                                    ->label('Deň')
                                                    ->options([
                                                        'monday' => 'Pondelok',
                                                        'tuesday' => 'Utorok',
                                                        'wednesday' => 'Streda',
                                                        'thursday' => 'Štvrtok',
                                                        'friday' => 'Piatok',
                                                        'saturday' => 'Sobota',
                                                        'sunday' => 'Nedeľa',
                                                    ])
                                                    ->required(),
                                                TimePicker::make('start_time')
                                                    ->label('Čas')
                                                    ->seconds(false),
                                            ])
                                            ->columns(2)
                                            ->orderColumn('sort_order')
                                            ->reorderable()
                                            ->defaultItems(0)
                                            ->addActionLabel('Pridať do rozvrhu')
                                            ->visible(fn (Get $get): bool => (bool) $get('is_recurring'))
                                            ->columnSpanFull(),
                                        TimePicker::make('start_time')
                                            ->label('Čas začiatku')
                                            ->visible(fn (Get $get): bool => ! $get('is_recurring')),
                                        DatePicker::make('event_date')
                                            ->label('Dátum')
                                            ->required(fn (Get $get): bool => ! $get('is_recurring'))
                                            ->visible(fn (Get $get): bool => ! $get('is_recurring')),
                                    ]),

                                Section::make('Kapacita a ceny')
                                    ->schema([
                                        TextInput::make('max_capacity')
                                            ->label('Max. kapacita')
                                            ->numeric()
                                            ->rule(function (?Model $record): ?\Closure {
                                                if (! $record) {
                                                    return null;
                                                }

                                                return function (string $attribute, mixed $value, \Closure $fail) use ($record): void {
                                                    if ($value === null) {
                                                        return;
                                                    }

                                                    $activeCount = $record->registrations()
                                                        ->where('status', RegistrationStatusEnum::Approved->value)
                                                        ->count();

                                                    if ((int) $value < $activeCount) {
                                                        $fail("Kapacita nemôže byť nižšia ako počet aktuálnych registrácií ({$activeCount}).");
                                                    }
                                                };
                                            }),
                                        Toggle::make('notify_on_available')
                                            ->label('Upozorniť pri voľnom mieste')
                                            ->helperText('Ak je tréning plný, používatelia sa môžu zapísať na čakací zoznam a budú notifikovaní, keď sa uvoľní miesto.'),
                                        Select::make('pricing_type')
                                            ->label('Typ ceny')
                                            ->options(fn (Get $get): array => $get('is_recurring')
                                                ? [
                                                    TrainingPricingTypeEnum::FREE->value => TrainingPricingTypeEnum::FREE->getLabel(),
                                                    TrainingPricingTypeEnum::MEMBERSHIP_REQUIRED->value => TrainingPricingTypeEnum::MEMBERSHIP_REQUIRED->getLabel(),
                                                ]
                                                : [
                                                    TrainingPricingTypeEnum::FREE->value => TrainingPricingTypeEnum::FREE->getLabel(),
                                                    TrainingPricingTypeEnum::PAID->value => TrainingPricingTypeEnum::PAID->getLabel(),
                                                ])
                                            ->required()
                                            ->default(TrainingPricingTypeEnum::FREE)
                                            ->live(),
                                        TextInput::make('price_amount')
                                            ->label('Cena')
                                            ->numeric()
                                            ->prefix('€')
                                            ->visible(fn (Get $get): bool => $get('pricing_type') === TrainingPricingTypeEnum::PAID->value),
                                        TextInput::make('payment_note')
                                            ->label('Poznámka platby (QR)')
                                            ->helperText('Dostupné premenné: {{meno}}, {{priezvisko}}, {{nazov_treningu}}, {{mesto}}, {{miesto}}, {{cas}}. Max 140 znakov (Pay by Square) / 60 znakov (QR Platba).')
                                            ->maxLength(140)
                                            ->visible(fn (Get $get): bool => $get('pricing_type') !== TrainingPricingTypeEnum::FREE->value),
                                        TextInput::make('bank_account_iban')
                                            ->label('IBAN')
                                            ->placeholder(fn (): string => Filament::getTenant()?->bank_account_iban ?? '')
                                            ->helperText(fn (): string => __('payments.bank_account_override.helper_text', ['default' => Filament::getTenant()?->bank_account_iban ?: '—']))
                                            ->visible(fn (Get $get): bool => $get('pricing_type') !== TrainingPricingTypeEnum::FREE->value),
                                        TextInput::make('bank_account_name')
                                            ->label('Názov príjemcu (override)')
                                            ->placeholder(fn (): string => Filament::getTenant()?->bank_account_name ?? '')
                                            ->helperText(fn (): string => __('payments.bank_account_override.recipient_helper_text', ['default' => Filament::getTenant()?->bank_account_name ?: '—']))
                                            ->visible(fn (Get $get): bool => $get('pricing_type') !== TrainingPricingTypeEnum::FREE->value),
                                    ]),
                            ]),

                        Tabs\Tab::make('Registrácia')
                            ->schema([
                                Section::make('Okno registrácie')
                                    ->description('Nastavte obdobie, kedy je registrácia otvorená. Ak ponecháte prázdne, registrácia bude otvorená bez obmedzení.')
                                    ->columns(2)
                                    ->schema([
                                        DateTimePicker::make('registration_opens_at')
                                            ->label('Registrácia sa otvorí'),
                                        DateTimePicker::make('registration_closes_at')
                                            ->label('Registrácia sa zatvorí'),
                                    ]),
                                Section::make('Registračný formulár')
                                    ->description('Definujte všetky polia registračného formulára. Povinné typy: Meno, Priezvisko, Email, Telefón.')
                                    ->schema([
                                        Repeater::make('registration_form_schema')
                                            ->label('Polia formulára')
                                            ->rule(function (): \Closure {
                                                return function (string $attribute, mixed $value, \Closure $fail): void {
                                                    if (! is_array($value)) {
                                                        return;
                                                    }
                                                    $types = collect($value)->pluck('type')->filter()->toArray();
                                                    $required = [
                                                        RegistrationFieldTypeEnum::FIRST_NAME->value => 'Meno',
                                                        RegistrationFieldTypeEnum::LAST_NAME->value => 'Priezvisko',
                                                        RegistrationFieldTypeEnum::EMAIL->value => 'Email',
                                                        RegistrationFieldTypeEnum::PHONE->value => 'Telefón',
                                                    ];
                                                    foreach ($required as $type => $label) {
                                                        if (! in_array($type, $types)) {
                                                            $fail("Formulár musí obsahovať pole typu {$label}.");
                                                        }
                                                    }
                                                };
                                            })
                                            ->itemLabel(fn (array $state): ?string => is_array($state['label'] ?? null) ? ($state['label']['sk'] ?? null) : ($state['label'] ?? null))
                                            ->columns(2)
                                            ->schema([
                                                Tabs::make('Preklady')
                                                    ->tabs([
                                                        Tabs\Tab::make('SK')->schema([
                                                            TextInput::make('label.sk')
                                                                ->label('Názov poľa (SK)')
                                                                ->required()
                                                                ->live(onBlur: true)
                                                                ->afterStateUpdated(fn (Set $set, ?string $state) => $set('name', Str::slug($state ?? '', '_'))),
                                                            TextInput::make('placeholder.sk')
                                                                ->label('Placeholder (SK)')
                                                                ->hidden(fn (Get $get): bool => in_array($get('type'), [
                                                                    RegistrationFieldTypeEnum::SELECT->value,
                                                                    RegistrationFieldTypeEnum::MULTI_SELECT->value,
                                                                    RegistrationFieldTypeEnum::DATE_PICKER->value,
                                                                    RegistrationFieldTypeEnum::YEAR_PICKER->value,
                                                                    RegistrationFieldTypeEnum::TIME_PICKER->value,
                                                                    RegistrationFieldTypeEnum::FILE_INPUT->value,
                                                                    RegistrationFieldTypeEnum::BIRTH_DATE->value,
                                                                    RegistrationFieldTypeEnum::GENDER->value,
                                                                ])),
                                                            RichEditor::make('helper_text.sk')
                                                                ->label('Pomocný text (SK)')
                                                                ->helperText('Zobrazí sa pod poľom v registračnom formulári.')
                                                                ->toolbarButtons(['bold', 'italic', 'link', 'bulletList', 'orderedList'])
                                                                ->columnSpanFull(),
                                                        ]),
                                                        Tabs\Tab::make('EN')->schema([
                                                            TextInput::make('label.en')->label('Label (EN)'),
                                                            TextInput::make('placeholder.en')
                                                                ->label('Placeholder (EN)')
                                                                ->hidden(fn (Get $get): bool => in_array($get('type'), [
                                                                    RegistrationFieldTypeEnum::SELECT->value,
                                                                    RegistrationFieldTypeEnum::MULTI_SELECT->value,
                                                                    RegistrationFieldTypeEnum::DATE_PICKER->value,
                                                                    RegistrationFieldTypeEnum::YEAR_PICKER->value,
                                                                    RegistrationFieldTypeEnum::TIME_PICKER->value,
                                                                    RegistrationFieldTypeEnum::FILE_INPUT->value,
                                                                    RegistrationFieldTypeEnum::BIRTH_DATE->value,
                                                                    RegistrationFieldTypeEnum::GENDER->value,
                                                                ])),
                                                            RichEditor::make('helper_text.en')
                                                                ->label('Helper text (EN)')
                                                                ->toolbarButtons(['bold', 'italic', 'link', 'bulletList', 'orderedList'])
                                                                ->columnSpanFull(),
                                                        ]),
                                                        Tabs\Tab::make('CS')->schema([
                                                            TextInput::make('label.cs')->label('Název pole (CS)'),
                                                            TextInput::make('placeholder.cs')
                                                                ->label('Placeholder (CS)')
                                                                ->hidden(fn (Get $get): bool => in_array($get('type'), [
                                                                    RegistrationFieldTypeEnum::SELECT->value,
                                                                    RegistrationFieldTypeEnum::MULTI_SELECT->value,
                                                                    RegistrationFieldTypeEnum::DATE_PICKER->value,
                                                                    RegistrationFieldTypeEnum::YEAR_PICKER->value,
                                                                    RegistrationFieldTypeEnum::TIME_PICKER->value,
                                                                    RegistrationFieldTypeEnum::FILE_INPUT->value,
                                                                    RegistrationFieldTypeEnum::BIRTH_DATE->value,
                                                                    RegistrationFieldTypeEnum::GENDER->value,
                                                                ])),
                                                            RichEditor::make('helper_text.cs')
                                                                ->label('Pomocný text (CS)')
                                                                ->toolbarButtons(['bold', 'italic', 'link', 'bulletList', 'orderedList'])
                                                                ->columnSpanFull(),
                                                        ]),
                                                    ])
                                                    ->columnSpanFull(),
                                                TextInput::make('name')
                                                    ->label('Kľúč')
                                                    ->required()
                                                    ->disabled()
                                                    ->dehydrated()
                                                    ->live(),
                                                Select::make('type')
                                                    ->label('Typ poľa')
                                                    ->options(collect(RegistrationFieldTypeEnum::cases())
                                                        ->reject(fn (RegistrationFieldTypeEnum $case): bool => $case === RegistrationFieldTypeEnum::CATEGORY)
                                                        ->mapWithKeys(fn (RegistrationFieldTypeEnum $case) => [$case->value => $case->getLabel()])
                                                        ->all())
                                                    ->required()
                                                    ->default(RegistrationFieldTypeEnum::TEXT_INPUT)
                                                    ->live()
                                                    ->disabled(fn (Get $get): bool => in_array($get('type'), [
                                                        RegistrationFieldTypeEnum::FIRST_NAME->value,
                                                        RegistrationFieldTypeEnum::LAST_NAME->value,
                                                        RegistrationFieldTypeEnum::EMAIL->value,
                                                        RegistrationFieldTypeEnum::PHONE->value,
                                                    ]))
                                                    ->dehydrated()
                                                    ->afterStateUpdated(function (Get $get, Set $set, mixed $state): void {
                                                        $label = $get('label.sk');
                                                        if ($label) {
                                                            $set('name', Str::slug($label, '_'));
                                                        } elseif ($state) {
                                                            $set('name', $state instanceof RegistrationFieldTypeEnum ? $state->value : $state);
                                                        }
                                                    }),
                                                Select::make('width')
                                                    ->label('Šírka')
                                                    ->options([
                                                        'half' => 'Polovica',
                                                        'full' => 'Celý riadok',
                                                    ])
                                                    ->default('half'),
                                                Toggle::make('required')
                                                    ->label('Povinné')
                                                    ->inline(false)
                                                    ->default(fn (Get $get): bool => in_array($get('type'), [
                                                        RegistrationFieldTypeEnum::FIRST_NAME->value,
                                                        RegistrationFieldTypeEnum::LAST_NAME->value,
                                                        RegistrationFieldTypeEnum::EMAIL->value,
                                                        RegistrationFieldTypeEnum::PHONE->value,
                                                    ]))
                                                    ->disabled(fn (Get $get): bool => in_array($get('type'), [
                                                        RegistrationFieldTypeEnum::FIRST_NAME->value,
                                                        RegistrationFieldTypeEnum::LAST_NAME->value,
                                                        RegistrationFieldTypeEnum::EMAIL->value,
                                                        RegistrationFieldTypeEnum::PHONE->value,
                                                    ]))
                                                    ->dehydrated(),
                                                Repeater::make('options')
                                                    ->label('Možnosti')
                                                    ->columnSpanFull()
                                                    ->table([
                                                        TableColumn::make('Kľúč'),
                                                        TableColumn::make('Názov (SK)'),
                                                        TableColumn::make('Názov (EN)'),
                                                        TableColumn::make('Název (CZ)'),
                                                    ])
                                                    ->schema([
                                                        TextInput::make('value')
                                                            ->required()
                                                            ->live(onBlur: true)
                                                            ->afterStateUpdated(function (?string $state, Set $set): void {
                                                                if ($state) {
                                                                    $set('value', Str::slug($state, '_'));
                                                                }
                                                            }),
                                                        TextInput::make('label.sk')
                                                            ->required()
                                                            ->live(onBlur: true)
                                                            ->afterStateUpdated(function (Get $get, Set $set, ?string $state): void {
                                                                if ($state && empty($get('value'))) {
                                                                    $set('value', Str::slug($state, '_'));
                                                                }
                                                            }),
                                                        TextInput::make('label.en'),
                                                        TextInput::make('label.cs'),
                                                    ])
                                                    ->defaultItems(0)
                                                    ->reorderable()
                                                    ->required(fn (Get $get): bool => in_array($get('type'), [
                                                        RegistrationFieldTypeEnum::SELECT->value,
                                                        RegistrationFieldTypeEnum::MULTI_SELECT->value,
                                                    ]))
                                                    ->visible(fn (Get $get): bool => in_array($get('type'), [
                                                        RegistrationFieldTypeEnum::SELECT->value,
                                                        RegistrationFieldTypeEnum::MULTI_SELECT->value,
                                                    ])),
                                                Section::make('Podmienka zobrazenia')
                                                    ->schema([
                                                        Toggle::make('has_condition')
                                                            ->label('Podmienené zobrazenie')
                                                            ->helperText('Zobraziť toto pole len ak iné pole má niektorú zo zadaných hodnôt (logický OR).')
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
                                                                        $label = is_array($item['label']) ? ($item['label']['sk'] ?? reset($item['label'])) : $item['label'];
                                                                        $options[$item['name']] = $label;
                                                                    }
                                                                }

                                                                return $options;
                                                            })
                                                            ->required(fn (Get $get): bool => (bool) $get('has_condition'))
                                                            ->hidden(fn (Get $get): bool => ! $get('has_condition')),
                                                        TagsInput::make('condition_values')
                                                            ->label('Očakávané hodnoty')
                                                            ->placeholder('napr. zena_50')
                                                            ->helperText('Pole sa zobrazí, ak referenčné pole má niektorú z týchto hodnôt. Pre select polia zadajte hodnoty (kľúče), nie zobrazované názvy.')
                                                            ->afterStateHydrated(function (TagsInput $component, $state, Get $get): void {
                                                                if (filled($state)) {
                                                                    return;
                                                                }
                                                                $legacy = $get('condition_value');
                                                                if (filled($legacy)) {
                                                                    $component->state(is_array($legacy) ? array_values($legacy) : [(string) $legacy]);
                                                                }
                                                            })
                                                            ->required(fn (Get $get): bool => (bool) $get('has_condition'))
                                                            ->hidden(fn (Get $get): bool => ! $get('has_condition')),
                                                    ])
                                                    ->collapsible()
                                                    ->collapsed()
                                                    ->columnSpanFull(),
                                            ])
                                            ->addActionLabel('Pridať pole')
                                            ->deleteAction(fn ($action) => $action
                                                ->requiresConfirmation()
                                                ->hidden(function (array $arguments, Repeater $component): bool {
                                                    $items = $component->getState();
                                                    $type = $items[$arguments['item']]['type'] ?? null;

                                                    return in_array($type, [
                                                        RegistrationFieldTypeEnum::FIRST_NAME->value,
                                                        RegistrationFieldTypeEnum::LAST_NAME->value,
                                                        RegistrationFieldTypeEnum::EMAIL->value,
                                                        RegistrationFieldTypeEnum::PHONE->value,
                                                    ]);
                                                }))
                                            ->default(RegistrationFieldOptions::defaultRequiredFields())
                                            ->reorderable()
                                            ->reorderableWithButtons()
                                            ->cloneable()
                                            ->collapsible()
                                            ->columnSpanFull(),
                                    ]),
                            ]),

                        Tabs\Tab::make('Potvrdzovací e-mail')
                            ->schema([
                                Section::make('Obsah potvrdzovacieho e-mailu')
                                    ->description('Voliteľný obsah, ktorý sa pridá do potvrdzovacieho e-mailu po registrácii.')
                                    ->schema([
                                        Tabs::make('E-mail preklady')
                                            ->tabs([
                                                Tabs\Tab::make('SK')
                                                    ->schema([
                                                        Mason::make('confirmation_email_content.sk')
                                                            ->label('Obsah e-mailu (SK)')
                                                            ->bricks(self::emailBricks())
                                                            ->previewLayout('mason.email-preview-layout'),
                                                    ]),
                                                Tabs\Tab::make('EN')
                                                    ->schema([
                                                        Mason::make('confirmation_email_content.en')
                                                            ->label('Obsah e-mailu (EN)')
                                                            ->bricks(self::emailBricks())
                                                            ->previewLayout('mason.email-preview-layout'),
                                                    ]),
                                                Tabs\Tab::make('CZ')
                                                    ->schema([
                                                        Mason::make('confirmation_email_content.cs')
                                                            ->label('Obsah e-mailu (CZ)')
                                                            ->bricks(self::emailBricks())
                                                            ->previewLayout('mason.email-preview-layout'),
                                                    ]),
                                            ])
                                            ->columnSpanFull(),
                                    ]),
                                Section::make('Prílohy e-mailu')
                                    ->description('Súbory, ktoré budú priložené k potvrdzujúcemu e-mailu (napr. pravidlá, pokyny, mapa).')
                                    ->schema([
                                        SpatieMediaLibraryFileUpload::make('email_attachments')
                                            ->label('Prílohy')
                                            ->collection('email_attachments')
                                            ->multiple()
                                            ->reorderable()
                                            ->maxSize(10240)
                                            ->helperText('Max. 10 MB na súbor. Podporované formáty: PDF, DOC, DOCX, JPG, PNG.')
                                            ->acceptedFileTypes([
                                                'application/pdf',
                                                'application/msword',
                                                'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                                                'image/jpeg',
                                                'image/png',
                                            ])
                                            ->columnSpanFull(),
                                    ]),
                            ]),

                        Tabs\Tab::make('Galéria')
                            ->schema([
                                FileUpload::make('gallery_images')
                                    ->label('Fotky')
                                    ->image()
                                    ->multiple()
                                    ->reorderable()
                                    ->panelLayout('grid')
                                    ->disk('public')
                                    ->directory('trainings/gallery')
                                    ->visibility('public')
                                    ->columnSpanFull(),
                            ]),

                        Tabs\Tab::make('Nastavenia')
                            ->columns(4)
                            ->schema([
                                TextInput::make('min_age')
                                    ->label('Min. vek')
                                    ->numeric()
                                    ->minValue(0)
                                    ->maxValue(99)
                                    ->placeholder('napr. 6'),
                                TextInput::make('max_age')
                                    ->label('Max. vek')
                                    ->numeric()
                                    ->minValue(0)
                                    ->maxValue(99)
                                    ->placeholder('napr. 18'),
                                Select::make('gender')
                                    ->label('Pohlavie')
                                    ->options(GenderEnum::class)
                                    ->placeholder('Všetky pohlavia'),
                                Toggle::make('is_active')
                                    ->label('Aktívny')
                                    ->inline(false)
                                    ->default(true),
                                Select::make('team_season_id')
                                    ->label('Sezóna')
                                    ->relationship(
                                        name: 'season',
                                        titleAttribute: 'name',
                                        modifyQueryUsing: fn ($query) => $query->orderByDesc('starts_at'),
                                    )
                                    ->placeholder('Bez sezóny')
                                    ->default(fn () => TeamSeason::query()
                                        ->where('starts_at', '<=', now())
                                        ->where('ends_at', '>=', now())
                                        ->first()?->id
                                    ),
                                Toggle::make('is_recurring_across_seasons')
                                    ->label('Opakovať v ďalšej sezóne')
                                    ->helperText('Tréning bude predvolene zaškrtnutý pri kopírovaní do novej sezóny')
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
