<?php

namespace App\Filament\Resources\Teams\Schemas;

use App\Enums\MembershipPeriodEnum;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use RalphJSmit\Filament\MediaLibrary\Filament\Forms\Components\MediaPicker;

class TeamForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('Preklady')
                    ->tabs([
                        Tabs\Tab::make('SK')
                            ->schema([
                                TextInput::make('name.sk')
                                    ->label('Názov (SK)')
                                    ->required(),
                                Textarea::make('story.sk')
                                    ->label('Príbeh (SK)')
                                    ->rows(4),
                                Textarea::make('achievements.sk')
                                    ->label('Úspechy (SK)')
                                    ->rows(3),
                            ]),
                        Tabs\Tab::make('EN')
                            ->schema([
                                TextInput::make('name.en')
                                    ->label('Názov (EN)'),
                                Textarea::make('story.en')
                                    ->label('Príbeh (EN)')
                                    ->rows(4),
                                Textarea::make('achievements.en')
                                    ->label('Úspechy (EN)')
                                    ->rows(3),
                            ]),
                        Tabs\Tab::make('CZ')
                            ->schema([
                                TextInput::make('name.cz')
                                    ->label('Názov (CZ)'),
                                Textarea::make('story.cz')
                                    ->label('Príbeh (CZ)')
                                    ->rows(4),
                                Textarea::make('achievements.cz')
                                    ->label('Úspechy (CZ)')
                                    ->rows(3),
                            ]),
                    ])
                    ->columnSpanFull(),
                TextInput::make('slug')
                    ->disabled()
                    ->dehydrated(),
                MediaPicker::make('logo')
                    ->label('Logo'),
                KeyValue::make('socials')
                    ->label('Sociálne siete')
                    ->keyLabel('Platforma')
                    ->valueLabel('URL'),
                Toggle::make('is_active')
                    ->label('Aktívny')
                    ->default(true),

                Section::make('Nastavenie členstva')
                    ->schema([
                        Toggle::make('membership_enabled')
                            ->label('Členstvo povolené')
                            ->live(),
                        TextInput::make('membership_fee_amount')
                            ->label('Suma členského')
                            ->numeric()
                            ->prefix('€')
                            ->visible(fn (Get $get): bool => (bool) $get('membership_enabled')),
                        Select::make('membership_fee_currency')
                            ->label('Mena')
                            ->options([
                                'EUR' => 'EUR',
                                'CZK' => 'CZK',
                                'USD' => 'USD',
                            ])
                            ->default('EUR')
                            ->visible(fn (Get $get): bool => (bool) $get('membership_enabled')),
                        Select::make('membership_period')
                            ->label('Obdobie')
                            ->options(MembershipPeriodEnum::translations())
                            ->visible(fn (Get $get): bool => (bool) $get('membership_enabled')),
                        Textarea::make('membership_description')
                            ->label('Popis členstva')
                            ->rows(3)
                            ->visible(fn (Get $get): bool => (bool) $get('membership_enabled')),
                    ])
                    ->collapsible()
                    ->columnSpanFull(),

                Section::make('Platobné údaje')
                    ->schema([
                        TextInput::make('bank_account_iban')
                            ->label('IBAN')
                            ->placeholder('SK89 7500 0000 0000 1234 5678'),
                        TextInput::make('bank_account_name')
                            ->label('Meno príjemcu'),
                        TextInput::make('stripe_connect_account_id')
                            ->label('Stripe Connect účet')
                            ->disabled()
                            ->dehydrated()
                            ->placeholder('Nepripojený'),
                    ])
                    ->collapsible()
                    ->columnSpanFull(),
            ]);
    }
}
