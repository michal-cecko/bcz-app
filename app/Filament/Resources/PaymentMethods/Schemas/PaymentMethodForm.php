<?php

namespace App\Filament\Resources\PaymentMethods\Schemas;

use App\Enums\PaymentMethodEnum;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
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
                TextInput::make('title')
                    ->label('Názov')
                    ->required()
                    ->placeholder('napr. Platba kartou'),
                RichEditor::make('description')
                    ->label('Popis')
                    ->toolbarButtons(['bold', 'italic', 'link', 'bulletList', 'orderedList'])
                    ->placeholder('Popis platobnej metódy zobrazený zákazníkovi...')
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
