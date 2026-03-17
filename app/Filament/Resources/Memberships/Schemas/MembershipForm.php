<?php

namespace App\Filament\Resources\Memberships\Schemas;

use App\Enums\MembershipPeriodEnum;
use App\Enums\MembershipStatusEnum;
use Filament\Facades\Filament;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
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
                Select::make('period')
                    ->label('Obdobie')
                    ->options(function (): array {
                        $team = Filament::getTenant();
                        $options = [];

                        if ($team?->membership_allow_monthly) {
                            $options[MembershipPeriodEnum::MONTHLY->value] = MembershipPeriodEnum::MONTHLY->getLabel();
                        }

                        if ($team?->membership_allow_yearly) {
                            $options[MembershipPeriodEnum::YEARLY->value] = MembershipPeriodEnum::YEARLY->getLabel();
                        }

                        return $options ?: MembershipPeriodEnum::translations();
                    })
                    ->required()
                    ->live()
                    ->afterStateUpdated(function (string $state, Set $set): void {
                        $team = Filament::getTenant();

                        if ($state === MembershipPeriodEnum::MONTHLY->value && $team?->membership_fee_amount_monthly) {
                            $set('fee_amount', (string) $team->membership_fee_amount_monthly);
                        } elseif ($state === MembershipPeriodEnum::YEARLY->value && $team?->membership_fee_amount_yearly) {
                            $set('fee_amount', (string) $team->membership_fee_amount_yearly);
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
