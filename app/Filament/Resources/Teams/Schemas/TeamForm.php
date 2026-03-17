<?php

namespace App\Filament\Resources\Teams\Schemas;

use App\Enums\TeamJoinModeEnum;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;

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
                                TextInput::make('name.cs')
                                    ->label('Názov (CZ)'),
                                Textarea::make('story.cs')
                                    ->label('Príbeh (CZ)')
                                    ->rows(4),
                                Textarea::make('achievements.cs')
                                    ->label('Úspechy (CZ)')
                                    ->rows(3),
                            ]),
                    ])
                    ->columnSpanFull(),
                TextInput::make('slug')
                    ->disabled()
                    ->dehydrated(),
                SpatieMediaLibraryFileUpload::make('logo')
                    ->collection('logo')
                    ->disk('public')
                    ->visibility('public')
                    ->label('Logo'),
                KeyValue::make('socials')
                    ->label('Sociálne siete')
                    ->keyLabel('Platforma')
                    ->valueLabel('URL'),
                Toggle::make('is_active')
                    ->label('Aktivny')
                    ->default(true),
                Select::make('join_mode')
                    ->label('Rezim pripojenia')
                    ->options(TeamJoinModeEnum::translations())
                    ->default(TeamJoinModeEnum::APPROVAL->value)
                    ->helperText('Otvoreny = automaticky prijaty, So schvalenim = admin musi schvalit'),

                Section::make('Nastavenie clenstva')
                    ->schema([
                        Toggle::make('membership_enabled')
                            ->label('Clenstvo povolene')
                            ->live(),
                        Select::make('membership_fee_currency')
                            ->label('Mena')
                            ->options([
                                'EUR' => 'EUR',
                                'CZK' => 'CZK',
                                'USD' => 'USD',
                            ])
                            ->default('EUR')
                            ->visible(fn (Get $get): bool => (bool) $get('membership_enabled')),
                        Textarea::make('membership_description')
                            ->label('Popis clenstva')
                            ->rows(3)
                            ->visible(fn (Get $get): bool => (bool) $get('membership_enabled'))
                            ->helperText('Sezonne clenstva sa spravuju v zalozke Sezony'),
                    ])
                    ->collapsible()
                    ->columnSpanFull(),

                Section::make('Platobne udaje')
                    ->schema([
                        TextInput::make('bank_account_iban')
                            ->label('IBAN')
                            ->placeholder('SK89 7500 0000 0000 1234 5678'),
                        TextInput::make('bank_account_name')
                            ->label('Meno prijemcu'),
                        TextInput::make('stripe_connect_account_id')
                            ->label('Stripe Connect ucet')
                            ->disabled()
                            ->dehydrated()
                            ->placeholder('Nepripojeny'),
                    ])
                    ->collapsible()
                    ->columnSpanFull(),
            ]);
    }
}
