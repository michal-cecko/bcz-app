<?php

namespace App\Filament\RelationManagers;

use App\Enums\PaymentMethodEnum;
use App\Enums\PaymentStatusEnum;
use App\Enums\RegistrationStatusEnum;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class RegistrationPaymentsRelationManager extends RelationManager
{
    protected static string $relationship = 'payments';

    protected static ?string $title = 'Platby';

    protected static ?string $modelLabel = 'platbu';

    protected static ?string $pluralModelLabel = 'Platby';

    public function isReadOnly(): bool
    {
        return false;
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('amount')
                    ->label('Suma')
                    ->numeric()
                    ->required()
                    ->prefix('€'),
                Select::make('currency')
                    ->label('Mena')
                    ->options(['EUR' => 'EUR', 'CZK' => 'CZK', 'USD' => 'USD'])
                    ->default('EUR')
                    ->required(),
                Select::make('payment_method')
                    ->label('Metóda platby')
                    ->options([
                        PaymentMethodEnum::BANK_TRANSFER->value => PaymentMethodEnum::BANK_TRANSFER->getLabel(),
                        PaymentMethodEnum::CASH->value => PaymentMethodEnum::CASH->getLabel(),
                        PaymentMethodEnum::GOPAY->value => PaymentMethodEnum::GOPAY->getLabel(),
                    ])
                    ->required()
                    ->default(PaymentMethodEnum::CASH),
                Select::make('status')
                    ->label('Stav')
                    ->options(PaymentStatusEnum::class)
                    ->required()
                    ->default(PaymentStatusEnum::COMPLETED),
                DateTimePicker::make('paid_at')
                    ->label('Dátum platby'),
                TextInput::make('variable_symbol')
                    ->label('Variabilný symbol'),
                TextInput::make('gopay_payment_id')
                    ->label('GoPay ID')
                    ->disabled()
                    ->dehydrated()
                    ->placeholder('-'),
                Textarea::make('notes')
                    ->label('Poznámka')
                    ->rows(2)
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->emptyStateHeading('Žiadne platby')
            ->emptyStateDescription('K tejto registrácii zatiaľ nie sú žiadne platby.')
            ->columns([
                TextColumn::make('amount')
                    ->label('Suma')
                    ->formatStateUsing(fn ($record): string => number_format((float) $record->amount, 2).' '.$record->currency),
                TextColumn::make('payment_method')
                    ->label('Metóda')
                    ->badge(),
                TextColumn::make('status')
                    ->label('Stav')
                    ->badge(),
                TextColumn::make('variable_symbol')
                    ->label('VS')
                    ->placeholder('-'),
                TextColumn::make('paid_at')
                    ->label('Zaplatené')
                    ->dateTime()
                    ->placeholder('-'),
                TextColumn::make('created_at')
                    ->label('Vytvorená')
                    ->dateTime()
                    ->sortable(),
            ])
            ->headerActions([
                CreateAction::make()
                    ->label('Pridať platbu')
                    ->mutateFormDataUsing(function (array $data): array {
                        $owner = $this->getOwnerRecord();
                        $data['team_id'] = method_exists($owner, 'training')
                            ? $owner->training?->team_id
                            : $owner->event?->team_id;
                        $data['user_id'] = $owner->user_id;
                        $data['payer_name'] = $owner->user?->name;
                        $data['payer_email'] = $owner->user?->email;

                        return $data;
                    })
                    ->after(function () {
                        $owner = $this->getOwnerRecord();
                        $status = $owner->status;

                        if ($status === RegistrationStatusEnum::Approved) {
                            return;
                        }

                        $totalPaid = $owner->payments()
                            ->where('status', PaymentStatusEnum::COMPLETED)
                            ->sum('amount');

                        $requiredAmount = method_exists($owner, 'training')
                            ? (float) ($owner->training?->price_amount ?? 0)
                            : (float) ($owner->event?->organization?->price_amount ?? 0);

                        if ($requiredAmount > 0 && $totalPaid >= $requiredAmount) {
                            $owner->update(['status' => RegistrationStatusEnum::Approved]);
                        }
                    }),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }
}
