<?php

namespace App\Filament\Resources\Trainings\Schemas;

use App\Enums\TrainingPricingTypeEnum;
use App\Models\Training;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class TrainingInfolist
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

                                Section::make('Miesto konania')
                                    ->icon('heroicon-o-map-pin')
                                    ->schema([
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
                                    ->columns(2)
                                    ->collapsible(),

                                Section::make('Kapacita a ceny')
                                    ->icon('heroicon-o-currency-euro')
                                    ->schema([
                                        TextEntry::make('pricing_type')
                                            ->label('Typ ceny')
                                            ->badge(),
                                        TextEntry::make('price_amount')
                                            ->label('Cena')
                                            ->money('EUR')
                                            ->visible(fn ($record): bool => $record->pricing_type === TrainingPricingTypeEnum::PAID),
                                        TextEntry::make('max_capacity')
                                            ->label('Max. kapacita')
                                            ->placeholder('Neobmedzená'),
                                        TextEntry::make('registrations_count')
                                            ->label('Registrácie')
                                            ->state(fn ($record): int => $record->registrations()->count()),
                                        IconEntry::make('notify_on_available')
                                            ->label('Upozornenie pri voľnom mieste')
                                            ->boolean()
                                            ->tooltip('Ak je tréning plný, používatelia sa môžu zapísať na čakací zoznam a budú notifikovaní, keď sa uvoľní miesto.'),
                                    ])
                                    ->columns(3),
                            ])
                            ->columnSpan(2),

                        Grid::make(1)
                            ->schema([
                                Section::make('Rozvrh')
                                    ->icon('heroicon-o-clock')
                                    ->schema([
                                        TextEntry::make('duration_minutes')
                                            ->label('Trvanie')
                                            ->suffix(' minút')
                                            ->placeholder('-'),
                                        TextEntry::make('start_time')
                                            ->label('Čas začiatku')
                                            ->placeholder('-'),
                                        TextEntry::make('schedule_days')
                                            ->label('Dni v týždni')
                                            ->badge()
                                            ->formatStateUsing(fn (string $state): string => match ($state) {
                                                'monday' => 'Po',
                                                'tuesday' => 'Ut',
                                                'wednesday' => 'St',
                                                'thursday' => 'Št',
                                                'friday' => 'Pi',
                                                'saturday' => 'So',
                                                'sunday' => 'Ne',
                                                default => $state,
                                            })
                                            ->color('primary'),
                                    ]),

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
                                        TextEntry::make('created_at')
                                            ->label('Vytvorené')
                                            ->dateTime('d.m.Y H:i'),
                                        TextEntry::make('updated_at')
                                            ->label('Upravené')
                                            ->dateTime('d.m.Y H:i'),
                                    ]),
                            ])
                            ->columnSpan(1),
                    ]),
            ]);
    }
}
