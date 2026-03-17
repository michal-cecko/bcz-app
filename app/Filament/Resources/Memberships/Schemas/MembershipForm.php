<?php

namespace App\Filament\Resources\Memberships\Schemas;

use App\Enums\MembershipStatusEnum;
use App\Models\TeamSeason;
use Filament\Facades\Filament;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;

class MembershipForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('user_id')
                    ->label('Používateľ')
                    ->relationship('user', 'name')
                    ->options(fn (): array => Filament::getTenant()
                        ->members()
                        ->pluck('name', 'users.id')
                        ->toArray())
                    ->searchable()
                    ->required(),
                Select::make('status')
                    ->label('Stav')
                    ->options(MembershipStatusEnum::translations())
                    ->default(MembershipStatusEnum::PENDING->value)
                    ->required(),
                Select::make('team_season_id')
                    ->label('Sezóna')
                    ->options(function (): array {
                        $team = Filament::getTenant();

                        return TeamSeason::where('team_id', $team?->id)
                            ->where('ends_at', '>=', now())
                            ->orderBy('starts_at', 'desc')
                            ->pluck('name', 'id')
                            ->toArray();
                    })
                    ->required()
                    ->live()
                    ->afterStateUpdated(function (?string $state, Set $set): void {
                        if (! $state) {
                            return;
                        }

                        $season = TeamSeason::find($state);

                        if ($season) {
                            $set('fee_amount', (string) $season->proratedFee());
                            $set('fee_currency', $season->fee_currency);
                            $set('starts_at', now()->toDateString());
                            $set('ends_at', $season->ends_at->toDateString());
                        }
                    }),
                Toggle::make('is_free')
                    ->label('Zadarmo')
                    ->live()
                    ->afterStateUpdated(function (bool $state, Set $set, Get $get): void {
                        if ($state) {
                            $set('fee_amount', '0');
                        } elseif ($get('team_season_id')) {
                            $season = TeamSeason::find($get('team_season_id'));
                            if ($season) {
                                $set('fee_amount', (string) $season->proratedFee());
                            }
                        }
                    }),
                TextInput::make('fee_amount')
                    ->label('Suma')
                    ->numeric()
                    ->required()
                    ->minValue(0),
                Select::make('fee_currency')
                    ->label('Mena')
                    ->options([
                        'EUR' => 'EUR',
                        'CZK' => 'CZK',
                        'USD' => 'USD',
                    ])
                    ->default('EUR')
                    ->required(),
                DatePicker::make('starts_at')
                    ->label('Začiatok')
                    ->required(),
                DatePicker::make('ends_at')
                    ->label('Koniec')
                    ->required()
                    ->after('starts_at'),
            ]);
    }
}
