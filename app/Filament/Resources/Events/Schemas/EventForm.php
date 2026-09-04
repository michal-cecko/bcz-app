<?php

namespace App\Filament\Resources\Events\Schemas;

use App\Enums\EventPricingTypeEnum;
use App\Enums\EventTypeEnum;
use App\Enums\RegistrationFieldTypeEnum;
use App\Filament\Support\PaymentNotePreview;
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
use App\Mason\Bricks\VideoSectionBrick;
use App\Mason\EmailBricks\EmailButtonBrick;
use App\Mason\EmailBricks\EmailCalloutBrick;
use App\Mason\EmailBricks\EmailDividerBrick;
use App\Mason\EmailBricks\EmailHeadingBrick;
use App\Mason\EmailBricks\EmailImageBrick;
use App\Mason\EmailBricks\EmailRichTextBrick;
use App\Mason\EmailBricks\EmailSpacerBrick;
use App\Models\Event;
use App\Models\EventOrganization;
use App\Support\ConditionFieldOptions;
use App\Support\RegistrationFieldOptions;
use Awcodes\Mason\Brick;
use Awcodes\Mason\Mason;
use Filament\Facades\Filament;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Repeater\TableColumn;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;

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
                            ->visible(fn (Get $get): bool => self::isOrganizedOrCompetition($get('event_type'))),
                        Tabs\Tab::make('Potvrdzovací e-mail')
                            ->icon('heroicon-o-envelope')
                            ->schema(self::confirmationEmailTab())
                            ->visible(fn (Get $get): bool => self::isOrganizedOrCompetition($get('event_type'))),
                        Tabs\Tab::make('Súťaž')
                            ->icon('heroicon-o-trophy')
                            ->schema(self::competitionTab())
                            ->visible(fn (Get $get): bool => self::isCompetition($get('event_type'))),
                        Tabs\Tab::make('Report (po ukončení)')
                            ->icon('heroicon-o-newspaper')
                            ->schema(self::reportContentTab())
                            ->visible(fn (Get $get): bool => self::isCompetition($get('event_type'))),
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
                                        ->label('Dátum publikovania')
                                        ->timezone(self::resolveEventTimezone()),
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
                                    Select::make('timezone')
                                        ->label('Časová zóna')
                                        ->options(collect(timezone_identifiers_list())->mapWithKeys(fn (string $tz) => [$tz => $tz]))
                                        ->default('Europe/Bratislava')
                                        ->searchable()
                                        ->visible(fn (Get $get): bool => self::isOrganizedOrCompetition($get('event_type'))),
                                    TextInput::make('attendee_count')
                                        ->label('Počet účastníkov')
                                        ->numeric()
                                        ->visible(fn (Get $get): bool => self::resolveEventType($get('event_type')) === EventTypeEnum::Report),
                                    TextInput::make('client')
                                        ->label('Klient')
                                        ->visible(fn (Get $get): bool => self::resolveEventType($get('event_type')) === EventTypeEnum::Report),
                                    SpatieMediaLibraryFileUpload::make('card_image')
                                        ->collection('card_image')
                                        ->disk('public')
                                        ->visibility('public')
                                        ->maxSize(10240)
                                        ->label('Obrázok na karte'),
                                    SpatieMediaLibraryFileUpload::make('detail_image')
                                        ->collection('detail_image')
                                        ->disk('public')
                                        ->visibility('public')
                                        ->maxSize(10240)
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
                                ->label('Suma (základná)')
                                ->helperText('Základná cena pre všetky kategórie. Pre konkrétne kategórie ju môžete prepísať v záložke "Poplatky za kategórie" (viditeľná po uložení súťažného podujatia).')
                                ->numeric()
                                ->minValue(0.01)
                                ->required(fn (Get $get): bool => self::isPaid($get('pricing_type')))
                                ->visible(fn (Get $get): bool => self::isPaid($get('pricing_type'))),
                            Select::make('price_currency')
                                ->label('Mena')
                                ->options([
                                    'EUR' => 'EUR',
                                    'CZK' => 'CZK',
                                    'USD' => 'USD',
                                ])
                                ->default('EUR')
                                ->visible(fn (Get $get): bool => self::isPaid($get('pricing_type'))),
                            TextInput::make('payment_note')
                                ->label('Poznámka platby (QR)')
                                ->default('{{meno}} {{priezvisko}}')
                                ->live(onBlur: true)
                                ->helperText(fn (?string $state): HtmlString => PaymentNotePreview::helperText($state, [
                                    'meno' => 'Ján',
                                    'priezvisko' => 'Novák',
                                    'nazov_eventu' => 'Jarná súťaž',
                                    'datum_eventu' => '15.05.2026',
                                    'miesto' => 'Mestský park',
                                ]))
                                ->maxLength(140)
                                ->visible(fn (Get $get): bool => self::isPaid($get('pricing_type'))),
                            TextInput::make('bank_account_iban')
                                ->label('IBAN')
                                ->placeholder(fn (): string => Filament::getTenant()?->bank_account_iban ?? '')
                                ->helperText(fn (): string => __('payments.bank_account_override.helper_text', ['default' => Filament::getTenant()?->bank_account_iban ?: '—']))
                                ->visible(fn (Get $get): bool => self::isPaid($get('pricing_type'))),
                            TextInput::make('bank_account_name')
                                ->label('Názov príjemcu (override)')
                                ->placeholder(fn (): string => Filament::getTenant()?->bank_account_name ?? '')
                                ->helperText(fn (): string => __('payments.bank_account_override.recipient_helper_text', ['default' => Filament::getTenant()?->bank_account_name ?: '—']))
                                ->visible(fn (Get $get): bool => self::isPaid($get('pricing_type'))),
                            DateTimePicker::make('registration_opens_at')
                                ->label('Registrácia od')
                                ->timezone(self::resolveEventTimezone()),
                            DateTimePicker::make('registration_closes_at')
                                ->label('Registrácia do')
                                ->timezone(self::resolveEventTimezone()),
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
                                                RegistrationFieldTypeEnum::CATEGORY->value,
                                            ])),
                                        RichEditor::make('helper_text.sk')
                                            ->label('Pomocný text (SK)')
                                            ->helperText('Zobrazí sa pod poľom v registračnom formulári.')
                                            ->toolbarButtons(['bold', 'italic', 'link', 'bulletList', 'orderedList'])
                                            ->columnSpanFull(),
                                    ]),
                                    Tabs\Tab::make('EN')->schema([
                                        TextInput::make('label.en')
                                            ->label('Label (EN)')
                                            ->live(onBlur: true),
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
                                                RegistrationFieldTypeEnum::CATEGORY->value,
                                            ])),
                                        RichEditor::make('helper_text.en')
                                            ->label('Helper text (EN)')
                                            ->toolbarButtons(['bold', 'italic', 'link', 'bulletList', 'orderedList'])
                                            ->columnSpanFull(),
                                    ]),
                                    Tabs\Tab::make('CS')->schema([
                                        TextInput::make('label.cs')
                                            ->label('Název pole (CS)')
                                            ->live(onBlur: true),
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
                                                RegistrationFieldTypeEnum::CATEGORY->value,
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
                            Section::make('Podmienené zobrazovanie')
                                ->columns(2)
                                ->schema([
                                    Toggle::make('has_condition')
                                        ->label('Aktivovať podmienené zobrazovanie')
                                        ->helperText('Zobraziť toto pole len ak iné pole má niektorú zo zadaných hodnôt (logický OR).')
                                        ->default(false)
                                        ->live()
                                        ->columnSpanFull(),
                                    Select::make('condition_field')
                                        ->label('Pole')
                                        ->helperText('Pole, od ktorého závisí zobrazenie')
                                        ->options(fn (Component $component): array => ConditionFieldOptions::forCurrent($component))
                                        ->live()
                                        ->required(fn (Get $get): bool => (bool) $get('has_condition'))
                                        ->hidden(fn (Get $get): bool => ! $get('has_condition')),
                                    Select::make('condition_values')
                                        ->label('Očakávané hodnoty')
                                        ->multiple()
                                        ->helperText('Pole sa zobrazí, ak referenčné pole má niektorú z vybraných hodnôt.')
                                        ->options(function (Get $get, ?Model $record): array {
                                            $sourceField = ConditionFieldOptions::findSourceField($get, $get('condition_field'));
                                            $event = $record instanceof EventOrganization ? $record->event : ($record instanceof Event ? $record : null);

                                            return ConditionFieldOptions::valueOptionsForSource($sourceField, app()->getLocale(), $event);
                                        })
                                        ->afterStateHydrated(function ($component, $state, Get $get): void {
                                            if (filled($state)) {
                                                return;
                                            }
                                            $legacy = $get('condition_value');
                                            if (filled($legacy)) {
                                                $component->state(is_array($legacy) ? array_values($legacy) : [(string) $legacy]);
                                            }
                                        })
                                        ->required(fn (Get $get): bool => (bool) $get('has_condition'))
                                        ->visible(function (Get $get): bool {
                                            if (! $get('has_condition')) {
                                                return false;
                                            }
                                            $sourceField = ConditionFieldOptions::findSourceField($get, $get('condition_field'));

                                            return ConditionFieldOptions::isOptionBased($sourceField);
                                        }),
                                    TagsInput::make('condition_values')
                                        ->label('Očakávané hodnoty')
                                        ->placeholder('napr. áno')
                                        ->helperText('Pole sa zobrazí, ak referenčné pole má niektorú z týchto hodnôt.')
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
                                        ->visible(function (Get $get): bool {
                                            if (! $get('has_condition')) {
                                                return false;
                                            }
                                            $sourceField = ConditionFieldOptions::findSourceField($get, $get('condition_field'));

                                            return ! ConditionFieldOptions::isOptionBased($sourceField);
                                        }),
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
        ];
    }

    private static function competitionTab(): array
    {
        return [
            Section::make('Manažér súťaže')
                ->description('Osoba zodpovedná za riadenie súťaže počas dňa konania.')
                ->relationship('competitionDetail')
                ->schema([
                    Select::make('manager_id')
                        ->label('Manažér')
                        ->relationship(
                            name: 'manager',
                            modifyQueryUsing: fn ($query, Get $get) => $query->whereHas('teams', function ($q) use ($get) {
                                $teamId = $get('../../team_id');
                                if ($teamId) {
                                    $q->where('teams.id', $teamId);
                                }
                            }),
                        )
                        ->getOptionLabelFromRecordUsing(fn (Model $record): string => "{$record->name} ({$record->email})")
                        ->searchable(['first_name', 'last_name', 'email'])
                        ->preload()
                        ->placeholder('Vyberte manažéra súťaže'),
                ]),
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

    private static function confirmationEmailTab(): array
    {
        return [
            Section::make('Obsah potvrdzovacieho e-mailu')
                ->description('Voliteľný obsah, ktorý sa pridá do potvrdzovacieho e-mailu po registrácii.')
                ->relationship('organization')
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
        ];
    }

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

    private static function resolveEventType(mixed $value): ?EventTypeEnum
    {
        if ($value instanceof EventTypeEnum) {
            return $value;
        }

        return EventTypeEnum::tryFrom((string) $value);
    }

    private static function isOrganizedOrCompetition(mixed $value): bool
    {
        return in_array(self::resolveEventType($value), [
            EventTypeEnum::Organized,
            EventTypeEnum::Competition,
        ], true);
    }

    private static function isCompetition(mixed $value): bool
    {
        return self::resolveEventType($value) === EventTypeEnum::Competition;
    }

    private static function isPaid(mixed $value): bool
    {
        if ($value instanceof EventPricingTypeEnum) {
            return $value === EventPricingTypeEnum::Paid;
        }

        return EventPricingTypeEnum::tryFrom((string) $value) === EventPricingTypeEnum::Paid;
    }

    /**
     * Resolve the timezone for a date/time picker bound to an Event or its
     * organization relationship. Falls back to the form's `timezone` field
     * during create and to Europe/Bratislava as a final default.
     */
    private static function resolveEventTimezone(): \Closure
    {
        return function (?Model $record, Get $get): string {
            if ($record instanceof EventOrganization) {
                $event = $record->event;
                if ($event instanceof Event) {
                    return $event->getTimezone();
                }
            }

            if ($record instanceof Event) {
                return $record->getTimezone();
            }

            $tz = $get('timezone');
            if (! is_string($tz) || $tz === '') {
                $tz = $get('../timezone');
            }

            return is_string($tz) && $tz !== '' ? $tz : 'Europe/Bratislava';
        };
    }

    private static function reportContentTab(): array
    {
        return [
            Section::make('Obsah reportu')
                ->description('Tento obsah sa zobrazí na stránke súťaže po jej ukončení — ako report/zhrnutie podujatia.')
                ->schema([
                    Tabs::make('Report preklady')
                        ->tabs([
                            Tabs\Tab::make('SK')
                                ->schema([
                                    Mason::make('report_content.sk')
                                        ->label('Report obsah (SK)')
                                        ->bricks(self::bricks())
                                        ->columnSpanFull(),
                                ]),
                            Tabs\Tab::make('EN')
                                ->schema([
                                    Mason::make('report_content.en')
                                        ->label('Report obsah (EN)')
                                        ->bricks(self::bricks())
                                        ->columnSpanFull(),
                                ]),
                            Tabs\Tab::make('CZ')
                                ->schema([
                                    Mason::make('report_content.cs')
                                        ->label('Report obsah (CZ)')
                                        ->bricks(self::bricks())
                                        ->columnSpanFull(),
                                ]),
                        ])
                        ->columnSpanFull(),
                ]),
            Section::make('Galéria')
                ->description('Fotografie z podujatia — zobrazia sa v záložke Report na verejnej stránke.')
                ->schema([
                    SpatieMediaLibraryFileUpload::make('gallery')
                        ->collection('gallery')
                        ->disk('public')
                        ->visibility('public')
                        ->multiple()
                        ->reorderable()
                        ->appendFiles()
                        ->image()
                        ->maxSize(10240)
                        ->label('Obrázky galérie')
                        ->columnSpanFull(),
                ]),
        ];
    }

    /** @return list<class-string<Brick>> */
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
            VideoSectionBrick::class,
            CompetitionResultsBrick::class,
            CompetitionBracketsBrick::class,
            CompetitionTimetableBrick::class,
        ];
    }
}
