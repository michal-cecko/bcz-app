<?php

namespace App\Filament\Resources\SubscriptionPlans\Schemas;

use App\Enums\PlanTierEnum;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Repeater\TableColumn;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Schema;

class SubscriptionPlanForm
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
                                Textarea::make('description.sk')
                                    ->label('Popis (SK)')
                                    ->rows(3),
                            ]),
                        Tabs\Tab::make('EN')
                            ->schema([
                                TextInput::make('name.en')
                                    ->label('Názov (EN)'),
                                Textarea::make('description.en')
                                    ->label('Popis (EN)')
                                    ->rows(3),
                            ]),
                        Tabs\Tab::make('CZ')
                            ->schema([
                                TextInput::make('name.cs')
                                    ->label('Názov (CZ)'),
                                Textarea::make('description.cs')
                                    ->label('Popis (CZ)')
                                    ->rows(3),
                            ]),
                    ])
                    ->columnSpanFull(),

                Select::make('tier')
                    ->label('Úroveň')
                    ->options(PlanTierEnum::translations())
                    ->required(),

                Section::make('Ceny')
                    ->schema([
                        Repeater::make('prices')
                            ->label('')
                            ->relationship()
                            ->table([
                                TableColumn::make('Mena'),
                                TableColumn::make('Mesačne'),
                                TableColumn::make('Ročne'),
                            ])
                            ->schema([
                                Select::make('currency_code')
                                    ->label('Mena')
                                    ->options([
                                        'EUR' => 'EUR',
                                        'CZK' => 'CZK',
                                        'USD' => 'USD',
                                    ])
                                    ->required(),
                                TextInput::make('price_monthly')
                                    ->label('Mesačne')
                                    ->numeric()
                                    ->required()
                                    ->minValue(0),
                                TextInput::make('price_yearly')
                                    ->label('Ročne')
                                    ->numeric()
                                    ->required()
                                    ->minValue(0),
                            ])
                            ->reorderable()
                            ->reorderableWithButtons()
                            ->cloneable()
                            ->collapsible(),
                    ])
                    ->columnSpanFull(),

                Section::make('Limity')
                    ->description('Kľúče: max_members, max_trainings, max_competitions_yearly, max_events_yearly, storage_limit_mb')
                    ->schema([
                        KeyValue::make('limits')
                            ->label('')
                            ->keyLabel('Kľúč')
                            ->valueLabel('Hodnota')
                            ->addActionLabel('Pridať limit'),
                    ])
                    ->columnSpanFull(),

                Toggle::make('is_active')
                    ->label('Aktívny')
                    ->default(true),

                TextInput::make('sort_order')
                    ->label('Poradie')
                    ->numeric()
                    ->default(0),

                TextInput::make('stripe_product_id')
                    ->label('Stripe Product ID')
                    ->placeholder('Voliteľné'),
            ]);
    }
}
