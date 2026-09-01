<?php

namespace App\Filament\Resources\Trainings\Schemas;

use App\Enums\RegistrationFieldTypeEnum;
use App\Enums\TrainingPricingTypeEnum;
use App\Models\Training;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\SpatieMediaLibraryImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Schema;

class TrainingInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('Tréning')
                    ->tabs([
                        Tabs\Tab::make('Základné')
                            ->schema(self::baseTab()),
                        Tabs\Tab::make('Miesto')
                            ->schema(self::locationTab()),
                        Tabs\Tab::make('Rozvrh a kapacita')
                            ->schema(self::scheduleTab()),
                        Tabs\Tab::make('Registrácia')
                            ->schema(self::registrationTab()),
                        Tabs\Tab::make('Potvrdzovací e-mail')
                            ->schema(self::confirmationEmailTab()),
                        Tabs\Tab::make('Galéria')
                            ->schema(self::galleryTab()),
                        Tabs\Tab::make('Nastavenia')
                            ->schema(self::settingsTab()),
                    ])
                    ->columnSpanFull(),
            ]);
    }

    private static function baseTab(): array
    {
        return [
            Section::make('Základné údaje')
                ->icon('heroicon-o-academic-cap')
                ->schema([
                    TextEntry::make('title')
                        ->label('Názov')
                        ->size('lg')
                        ->weight('bold'),
                    TextEntry::make('description')
                        ->label('Popis')
                        ->placeholder('Bez popisu')
                        ->html()
                        ->columnSpanFull(),
                    TextEntry::make('sportCategory.name')
                        ->label('Športová kategória')
                        ->badge()
                        ->color('primary'),
                    TextEntry::make('slug')
                        ->label('URL slug')
                        ->color('gray')
                        ->copyable(),
                ])
                ->columns(2),
        ];
    }

    private static function locationTab(): array
    {
        return [
            Section::make('Miesto konania')
                ->icon('heroicon-o-map-pin')
                ->schema([
                    TextEntry::make('city.name')
                        ->label('Mesto')
                        ->badge()
                        ->color('primary')
                        ->placeholder('-'),
                    TextEntry::make('place_name')
                        ->label('Názov miesta')
                        ->placeholder('-'),
                    TextEntry::make('place_address')
                        ->label('Adresa')
                        ->placeholder('-'),
                    TextEntry::make('gathering_place')
                        ->label('Miesto stretnutia')
                        ->placeholder('-'),
                    TextEntry::make('latitude')
                        ->label('Súradnice')
                        ->placeholder('-')
                        ->formatStateUsing(fn ($record): ?string => $record->latitude && $record->longitude
                            ? "{$record->latitude}, {$record->longitude}"
                            : null),
                ])
                ->columns(2),
        ];
    }

    /**
     * Read-only summary of the season the training belongs to. Display only – season
     * data is edited in the Sezóny resource.
     */
    private static function seasonCard(): Section
    {
        return Section::make('Aktuálna sezóna')
            ->icon('heroicon-o-calendar-days')
            ->description(function (Training $record): string {
                $sentences = [];

                if ($record->season !== null && ! $record->season->isActive()) {
                    $sentences[] = 'Táto sezóna už nie je aktuálna.';
                }

                if ($record->season?->monthlyFee() !== null) {
                    $sentences[] = 'Mesačná suma je orientačná – cena sezóny delená počtom mesiacov jej trvania.';
                }

                return implode(' ', $sentences);
            })
            ->columns(2)
            ->columnSpanFull()
            ->visible(fn (Training $record): bool => $record->season !== null)
            ->schema([
                TextEntry::make('season.name')
                    ->label('Názov sezóny')
                    ->placeholder('-'),
                TextEntry::make('season_fee_amount')
                    ->label('Cena sezóny')
                    ->placeholder('-')
                    ->state(function (Training $record): ?string {
                        $season = $record->season;

                        if ($season === null || $season->fee_amount === null) {
                            return null;
                        }

                        return number_format((float) $season->fee_amount, 2).' '.$season->fee_currency;
                    }),
                TextEntry::make('season_monthly_fee')
                    ->label('Cena za mesiac')
                    ->visible(fn (Training $record): bool => $record->season?->monthlyFee() !== null)
                    ->state(function (Training $record): ?string {
                        $season = $record->season;
                        $monthlyFee = $season?->monthlyFee();

                        if ($season === null || $monthlyFee === null) {
                            return null;
                        }

                        return number_format($monthlyFee, 2).' '.$season->fee_currency;
                    }),
            ]);
    }

    private static function scheduleTab(): array
    {
        return [
            Grid::make(2)
                ->schema([
                    Section::make('Rozvrh')
                        ->icon('heroicon-o-clock')
                        ->schema([
                            IconEntry::make('is_recurring')
                                ->label('Pravidelný tréning')
                                ->boolean(),
                            TextEntry::make('duration_minutes')
                                ->label('Trvanie')
                                ->suffix(' minút')
                                ->placeholder('-'),
                            TextEntry::make('start_time')
                                ->label('Čas začiatku')
                                ->placeholder('-')
                                ->visible(fn ($record): bool => ! $record->is_recurring),
                            RepeatableEntry::make('schedules')
                                ->label('Rozvrh')
                                ->schema([
                                    TextEntry::make('day')
                                        ->label('Deň')
                                        ->badge()
                                        ->formatStateUsing(fn (string $state): string => match ($state) {
                                            'monday' => 'Pondelok',
                                            'tuesday' => 'Utorok',
                                            'wednesday' => 'Streda',
                                            'thursday' => 'Štvrtok',
                                            'friday' => 'Piatok',
                                            'saturday' => 'Sobota',
                                            'sunday' => 'Nedeľa',
                                            default => $state,
                                        })
                                        ->color('primary'),
                                    TextEntry::make('start_time')
                                        ->label('Čas')
                                        ->placeholder('-'),
                                ])
                                ->columns(2)
                                ->visible(fn ($record): bool => (bool) $record->is_recurring),
                            TextEntry::make('event_date')
                                ->label('Dátum')
                                ->date('d.m.Y')
                                ->placeholder('-')
                                ->visible(fn ($record): bool => ! $record->is_recurring),
                        ]),

                    Section::make('Kapacita a ceny')
                        ->icon('heroicon-o-currency-euro')
                        ->schema([
                            self::seasonCard(),
                            TextEntry::make('pricing_type')
                                ->label('Typ ceny')
                                ->badge(),
                            TextEntry::make('price_amount')
                                ->label('Cena')
                                ->money('EUR')
                                ->visible(fn ($record): bool => $record->pricing_type === TrainingPricingTypeEnum::PAID),
                            TextEntry::make('payment_note')
                                ->label('Poznámka platby')
                                ->placeholder('-')
                                ->visible(fn ($record): bool => $record->pricing_type !== TrainingPricingTypeEnum::FREE),
                            TextEntry::make('max_capacity')
                                ->label('Max. kapacita')
                                ->placeholder('Neobmedzená'),
                            TextEntry::make('registrations_count')
                                ->label('Registrácie')
                                ->state(fn ($record): int => $record->registrations()->count()),
                            IconEntry::make('notify_on_available')
                                ->label('Upozornenie pri voľnom mieste')
                                ->boolean(),
                        ]),
                ]),
        ];
    }

    private static function registrationTab(): array
    {
        return [
            Section::make('Okno registrácie')
                ->schema([
                    TextEntry::make('registration_opens_at')
                        ->label('Registrácia sa otvorí')
                        ->dateTime('d.m.Y H:i')
                        ->placeholder('Bez obmedzenia'),
                    TextEntry::make('registration_closes_at')
                        ->label('Registrácia sa zatvorí')
                        ->dateTime('d.m.Y H:i')
                        ->placeholder('Bez obmedzenia'),
                ])
                ->columns(2),

            Section::make('Registračný formulár')
                ->schema([
                    RepeatableEntry::make('registration_form_schema')
                        ->label('Schéma formuláru')
                        ->schema([
                            TextEntry::make('label.sk')
                                ->label('Názov'),
                            TextEntry::make('type')
                                ->label('Typ')
                                ->badge()
                                ->formatStateUsing(fn (string $state): string => RegistrationFieldTypeEnum::tryFrom($state)?->getLabel() ?? $state),
                            IconEntry::make('required')
                                ->label('Povinné')
                                ->boolean(),
                        ])
                        ->columns(3)
                        ->placeholder('Žiadne polia formulára'),
                ]),
        ];
    }

    private static function confirmationEmailTab(): array
    {
        return [
            Section::make('Obsah potvrdzovacieho e-mailu')
                ->schema([
                    TextEntry::make('confirmation_email_content')
                        ->label('Obsah e-mailu')
                        ->placeholder('Žiadny vlastný obsah e-mailu')
                        ->formatStateUsing(function ($state): string {
                            if (empty($state)) {
                                return '';
                            }

                            $locales = [];
                            $content = is_string($state) ? json_decode($state, true) : $state;
                            if (is_array($content)) {
                                foreach ($content as $locale => $bricks) {
                                    if (! empty($bricks)) {
                                        $locales[] = strtoupper($locale);
                                    }
                                }
                            }

                            return $locales ? 'Nakonfigurované pre: '.implode(', ', $locales) : '';
                        })
                        ->columnSpanFull(),
                ]),
            Section::make('Prílohy e-mailu')
                ->schema([
                    SpatieMediaLibraryImageEntry::make('email_attachments')
                        ->label('Prílohy')
                        ->collection('email_attachments')
                        ->placeholder('Žiadne prílohy')
                        ->columnSpanFull(),
                ]),
        ];
    }

    private static function galleryTab(): array
    {
        return [
            Section::make('Galéria')
                ->schema([
                    ImageEntry::make('gallery_images')
                        ->label('Fotky')
                        ->disk('public')
                        ->placeholder('Žiadne fotky')
                        ->columnSpanFull(),
                ]),
        ];
    }

    private static function settingsTab(): array
    {
        return [
            Section::make('Nastavenia')
                ->icon('heroicon-o-cog-6-tooth')
                ->schema([
                    TextEntry::make('age_range')
                        ->label('Veková skupina')
                        ->state(function (Training $record): string {
                            if ($record->min_age === null && $record->max_age === null) {
                                return 'Všetky';
                            }
                            if ($record->max_age === null) {
                                return $record->min_age.'+';
                            }
                            if ($record->min_age === null) {
                                return 'do '.$record->max_age;
                            }

                            return $record->min_age.'-'.$record->max_age;
                        }),
                    TextEntry::make('gender')
                        ->label('Pohlavie')
                        ->badge()
                        ->placeholder('Všetky'),
                    IconEntry::make('is_active')
                        ->label('Aktívny')
                        ->boolean(),
                    TextEntry::make('season.name')
                        ->label('Sezóna')
                        ->placeholder('Bez sezóny'),
                    IconEntry::make('is_recurring_across_seasons')
                        ->label('Opakovať v ďalšej sezóne')
                        ->boolean(),
                    TextEntry::make('sort_order')
                        ->label('Poradie'),
                    TextEntry::make('created_at')
                        ->label('Vytvorené')
                        ->dateTime('d.m.Y H:i'),
                    TextEntry::make('updated_at')
                        ->label('Upravené')
                        ->dateTime('d.m.Y H:i'),
                ])
                ->columns(3),
        ];
    }
}
