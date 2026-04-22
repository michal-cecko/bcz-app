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
use App\Mason\Bricks\VideoSectionBrick;
use App\Mason\EmailBricks\EmailButtonBrick;
use App\Mason\EmailBricks\EmailCalloutBrick;
use App\Mason\EmailBricks\EmailDividerBrick;
use App\Mason\EmailBricks\EmailHeadingBrick;
use App\Mason\EmailBricks\EmailImageBrick;
use App\Mason\EmailBricks\EmailRichTextBrick;
use App\Mason\EmailBricks\EmailSpacerBrick;
use Awcodes\Mason\Brick;
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
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Model;
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
                            TextInput::make('variable_symbol')
                                ->label('Variabilný symbol')
                                ->maxLength(10)
                                ->visible(fn (Get $get): bool => self::isPaid($get('pricing_type'))),
                            TextInput::make('payment_note')
                                ->label('Poznámka platby (QR)')
                                ->helperText('Dostupné premenné: {{meno}}, {{priezvisko}}, {{nazov_eventu}}, {{datum_eventu}}, {{miesto}}. Max 140 znakov (Pay by Square) / 60 znakov (QR Platba).')
                                ->maxLength(140)
                                ->visible(fn (Get $get): bool => self::isPaid($get('pricing_type'))),
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
                                ->disabled()
                                ->dehydrated(),
                            Select::make('type')
                                ->label('Typ')
                                ->options(RegistrationFieldTypeEnum::class)
                                ->required()
                                ->live()
                                ->disabled(fn (Get $get): bool => in_array($get('type'), [
                                    RegistrationFieldTypeEnum::FIRST_NAME->value,
                                    RegistrationFieldTypeEnum::LAST_NAME->value,
                                    RegistrationFieldTypeEnum::EMAIL->value,
                                    RegistrationFieldTypeEnum::PHONE->value,
                                ]))
                                ->dehydrated()
                                ->afterStateUpdated(function (Get $get, Set $set, mixed $state): void {
                                    $label = $get('label');
                                    if ($label) {
                                        $set('key', Str::slug($label, '_'));
                                    } elseif ($state) {
                                        $set('key', $state instanceof RegistrationFieldTypeEnum ? $state->value : $state);
                                    }
                                }),
                            TextInput::make('label')
                                ->label('Označenie')
                                ->required()
                                ->live(onBlur: true)
                                ->afterStateUpdated(fn (Set $set, ?string $state) => $set('key', Str::slug($state ?? '', '_'))),
                            Toggle::make('required')
                                ->label('Povinné')
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
                            Textarea::make('options')
                                ->label('Možnosti (jedna na riadok)')
                                ->rows(3)
                                ->helperText('Pre select/multi_select')
                                ->visible(fn (Get $get): bool => in_array($get('type'), [
                                    RegistrationFieldTypeEnum::SELECT->value,
                                    RegistrationFieldTypeEnum::MULTI_SELECT->value,
                                ])),
                        ])
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
                        ->defaultItems(0)
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
