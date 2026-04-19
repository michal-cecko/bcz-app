<?php

namespace App\Filament\Resources\Events\Schemas;

use App\Enums\EventPricingTypeEnum;
use App\Enums\EventTypeEnum;
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
use App\Models\Event;
use Awcodes\Mason\MasonEntry;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\SpatieMediaLibraryImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Schema;

class EventInfolist
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
                        Tabs\Tab::make('Popis')
                            ->icon('heroicon-o-document-text')
                            ->schema(self::contentTab()),
                        Tabs\Tab::make('Organizácia')
                            ->icon('heroicon-o-clipboard-document-list')
                            ->schema(self::organizationTab())
                            ->visible(fn (Event $record): bool => self::isOrganizedOrCompetition($record->event_type)),
                        Tabs\Tab::make('Súťaž')
                            ->icon('heroicon-o-trophy')
                            ->schema(self::competitionTab())
                            ->visible(fn (Event $record): bool => $record->event_type === EventTypeEnum::Competition),
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
                    Grid::make(1)
                        ->schema([
                            Section::make('Obsah')
                                ->schema([
                                    TextEntry::make('event_type')
                                        ->label('Typ podujatia')
                                        ->badge(),
                                    TextEntry::make('title')
                                        ->label('Názov')
                                        ->size('lg')
                                        ->weight('bold'),
                                    TextEntry::make('card_description')
                                        ->label('Popis na karte')
                                        ->placeholder('Bez popisu'),
                                    TextEntry::make('slug')
                                        ->label('URL slug')
                                        ->color('gray')
                                        ->copyable(),
                                ]),
                            Section::make('Obrázky')
                                ->schema([
                                    SpatieMediaLibraryImageEntry::make('card_image')
                                        ->collection('card_image')
                                        ->label('Obrázok na karte')
                                        ->placeholder('-'),
                                    SpatieMediaLibraryImageEntry::make('detail_image')
                                        ->collection('detail_image')
                                        ->label('Obrázok detailu')
                                        ->placeholder('-'),
                                ]),
                        ])
                        ->columnSpan(2),

                    Grid::make(1)
                        ->schema([
                            Section::make('Publikovanie')
                                ->schema([
                                    IconEntry::make('is_published')
                                        ->label('Publikované')
                                        ->boolean(),
                                    TextEntry::make('published_at')
                                        ->label('Dátum publikovania')
                                        ->dateTime('d.m.Y H:i')
                                        ->placeholder('-'),
                                    TextEntry::make('eventCategory.title')
                                        ->label('Kategória')
                                        ->badge()
                                        ->color('primary'),
                                ]),

                            Section::make('Detaily')
                                ->schema([
                                    TextEntry::make('date')
                                        ->label('Dátum')
                                        ->date('d.m.Y'),
                                    TextEntry::make('date_end')
                                        ->label('Dátum konca')
                                        ->date('d.m.Y')
                                        ->placeholder('-'),
                                    TextEntry::make('country')
                                        ->label('Krajina')
                                        ->placeholder('-'),
                                    TextEntry::make('city')
                                        ->label('Mesto')
                                        ->placeholder('-'),
                                    TextEntry::make('place_name')
                                        ->label('Názov miesta')
                                        ->placeholder('-'),
                                    TextEntry::make('place_address')
                                        ->label('Adresa')
                                        ->placeholder('-'),
                                    TextEntry::make('timezone')
                                        ->label('Časová zóna')
                                        ->placeholder('Europe/Bratislava')
                                        ->visible(fn (Event $record): bool => self::isOrganizedOrCompetition($record->event_type)),
                                    TextEntry::make('attendee_count')
                                        ->label('Počet účastníkov')
                                        ->placeholder('-')
                                        ->visible(fn (Event $record): bool => $record->event_type === EventTypeEnum::Report),
                                    TextEntry::make('client')
                                        ->label('Klient')
                                        ->placeholder('-')
                                        ->visible(fn (Event $record): bool => $record->event_type === EventTypeEnum::Report),
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
                ->schema([
                    Grid::make(2)
                        ->schema([
                            TextEntry::make('organization.max_capacity')
                                ->label('Max. kapacita')
                                ->placeholder('Neobmedzená'),
                            TextEntry::make('organization.pricing_type')
                                ->label('Typ ceny')
                                ->badge(),
                            TextEntry::make('organization.price_amount')
                                ->label('Suma')
                                ->money('EUR')
                                ->visible(fn (Event $record): bool => $record->organization?->pricing_type === EventPricingTypeEnum::Paid),
                            TextEntry::make('organization.price_currency')
                                ->label('Mena')
                                ->visible(fn (Event $record): bool => $record->organization?->pricing_type === EventPricingTypeEnum::Paid),
                            TextEntry::make('organization.registration_opens_at')
                                ->label('Registrácia od')
                                ->dateTime('d.m.Y H:i')
                                ->placeholder('-'),
                            TextEntry::make('organization.registration_closes_at')
                                ->label('Registrácia do')
                                ->dateTime('d.m.Y H:i')
                                ->placeholder('-'),
                            IconEntry::make('organization.is_public_registration')
                                ->label('Verejná registrácia')
                                ->boolean(),
                            IconEntry::make('organization.show_countdown')
                                ->label('Odpočítavanie')
                                ->boolean(),
                            TextEntry::make('organization.external_link')
                                ->label('Externý odkaz')
                                ->url(fn ($state): ?string => $state)
                                ->placeholder('-')
                                ->columnSpanFull(),
                        ]),
                ]),
            Section::make('Registračný formulár')
                ->schema([
                    RepeatableEntry::make('organization.registration_form_schema')
                        ->label('Schéma formuláru')
                        ->schema([
                            TextEntry::make('label')
                                ->label('Označenie'),
                            TextEntry::make('type')
                                ->label('Typ')
                                ->badge(),
                            IconEntry::make('required')
                                ->label('Povinné')
                                ->boolean(),
                        ])
                        ->columns(3)
                        ->placeholder('Žiadne polia formulára'),
                ]),
        ];
    }

    private static function competitionTab(): array
    {
        return [
            Section::make('Manažér súťaže')
                ->description('Osoba zodpovedná za riadenie súťaže počas dňa konania.')
                ->schema([
                    TextEntry::make('competitionDetail.manager.name')
                        ->label('Manažér')
                        ->placeholder('Nepriradený'),
                ]),
            Section::make('Kategórie a disciplíny')
                ->schema([
                    TextEntry::make('competitionDetail.athleteCategories.name')
                        ->label('Kategórie atlétov')
                        ->badge()
                        ->color('primary')
                        ->placeholder('-'),
                    TextEntry::make('competitionDetail.disciplines.name')
                        ->label('Disciplíny')
                        ->badge()
                        ->color('info')
                        ->placeholder('-'),
                ]),
        ];
    }

    private static function contentTab(): array
    {
        return [
            Tabs::make('Obsah preklady')
                ->tabs([
                    Tabs\Tab::make('SK')
                        ->schema([
                            MasonEntry::make('content_sk')
                                ->label('Obsah (SK)')
                                ->state(fn (Event $record): array => $record->getTranslation('content', 'sk') ?? [])
                                ->bricks(self::bricks())
                                ->columnSpanFull(),
                        ]),
                    Tabs\Tab::make('EN')
                        ->schema([
                            MasonEntry::make('content_en')
                                ->label('Obsah (EN)')
                                ->state(fn (Event $record): array => $record->getTranslation('content', 'en') ?? [])
                                ->bricks(self::bricks())
                                ->columnSpanFull(),
                        ]),
                    Tabs\Tab::make('CZ')
                        ->schema([
                            MasonEntry::make('content_cs')
                                ->label('Obsah (CZ)')
                                ->state(fn (Event $record): array => $record->getTranslation('content', 'cs') ?? [])
                                ->bricks(self::bricks())
                                ->columnSpanFull(),
                        ]),
                ])
                ->columnSpanFull(),
        ];
    }

    /** @return list<class-string> */
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

    private static function isOrganizedOrCompetition(?EventTypeEnum $type): bool
    {
        return in_array($type, [EventTypeEnum::Organized, EventTypeEnum::Competition], true);
    }
}
