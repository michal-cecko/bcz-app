<?php

namespace App\Filament\Resources\TeamSeasons\Schemas;

use App\Rules\NoOverlappingSeason;
use Filament\Facades\Filament;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class TeamSeasonForm
{
    public static function configure(Schema $schema): Schema
    {
        $teamId = Filament::getTenant()?->id ?? '';
        $recordId = $schema->getModel()?->id ?? null;

        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Názov')
                    ->required(),
                DatePicker::make('starts_at')
                    ->label('Začiatok')
                    ->required()
                    ->rules([new NoOverlappingSeason($teamId, $recordId)]),
                DatePicker::make('ends_at')
                    ->label('Koniec')
                    ->required()
                    ->after('starts_at')
                    ->rules([new NoOverlappingSeason($teamId, $recordId)]),
                TextInput::make('fee_amount')
                    ->label('Suma')
                    ->numeric()
                    ->required()
                    ->minValue(0),
                Select::make('fee_currency')
                    ->label('Mena')
                    ->options(['EUR' => 'EUR', 'CZK' => 'CZK', 'USD' => 'USD'])
                    ->default('EUR')
                    ->required(),
                TextInput::make('variable_symbol')
                    ->label('Variabilný symbol')
                    ->maxLength(10),
                TextInput::make('payment_note')
                    ->label('Poznámka platby (QR)')
                    ->helperText('Dostupné premenné: {{meno}}, {{priezvisko}}, {{sezona}}, {{nazov_timu}}. Max 140 znakov (Pay by Square) / 60 znakov (QR Platba).')
                    ->maxLength(140),
                TextInput::make('max_capacity')
                    ->label('Maximálny počet členov')
                    ->numeric()
                    ->nullable(),
                TextInput::make('payment_deadline_days')
                    ->label('Splatnosť (dní)')
                    ->numeric()
                    ->default(14)
                    ->required(),
            ]);
    }
}
