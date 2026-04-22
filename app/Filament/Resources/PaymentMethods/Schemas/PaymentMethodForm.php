<?php

namespace App\Filament\Resources\PaymentMethods\Schemas;

use App\Enums\PaymentMethodEnum;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Schema;

class PaymentMethodForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('method')
                    ->label('Metóda')
                    ->options([
                        PaymentMethodEnum::GOPAY->value => 'GoPay (platba kartou)',
                        PaymentMethodEnum::BANK_TRANSFER->value => 'Bankový prevod',
                        PaymentMethodEnum::CASH->value => 'Hotovosť',
                    ])
                    ->required()
                    ->disabled()
                    ->dehydrated(),
                Tabs::make('Preklady')
                    ->tabs([
                        Tabs\Tab::make('SK')
                            ->schema([
                                TextInput::make('title.sk')
                                    ->label('Názov (SK)')
                                    ->required()
                                    ->placeholder('napr. Platba kartou'),
                                RichEditor::make('description.sk')
                                    ->label('Popis (SK)')
                                    ->toolbarButtons(['bold', 'italic', 'link', 'bulletList', 'orderedList'])
                                    ->placeholder('Popis platobnej metódy zobrazený zákazníkovi...')
                                    ->columnSpanFull(),
                            ]),
                        Tabs\Tab::make('EN')
                            ->schema([
                                TextInput::make('title.en')
                                    ->label('Názov (EN)'),
                                RichEditor::make('description.en')
                                    ->label('Popis (EN)')
                                    ->toolbarButtons(['bold', 'italic', 'link', 'bulletList', 'orderedList'])
                                    ->columnSpanFull(),
                            ]),
                        Tabs\Tab::make('CZ')
                            ->schema([
                                TextInput::make('title.cs')
                                    ->label('Názov (CZ)'),
                                RichEditor::make('description.cs')
                                    ->label('Popis (CZ)')
                                    ->toolbarButtons(['bold', 'italic', 'link', 'bulletList', 'orderedList'])
                                    ->columnSpanFull(),
                            ]),
                    ])
                    ->columnSpanFull(),
                Toggle::make('is_active')
                    ->label('Aktívna')
                    ->default(true),
                TextInput::make('sort_order')
                    ->label('Poradie')
                    ->numeric()
                    ->default(0),
            ]);
    }
}
