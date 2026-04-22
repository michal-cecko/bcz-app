<?php

namespace App\Filament\Resources\TeamSeasons\RelationManagers;

use App\Enums\MembershipStatusEnum;
use App\Enums\PaymentMethodEnum;
use App\Models\Membership;
use App\Services\PaymentService;
use App\Services\SeasonService;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class MembershipsRelationManager extends RelationManager
{
    protected static string $relationship = 'memberships';

    protected static ?string $title = 'Členstvá';

    protected static ?string $modelLabel = 'členstvo';

    protected static ?string $pluralModelLabel = 'Členstvá';

    public function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')
                    ->label('Používateľ')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('status')
                    ->label('Stav')
                    ->badge()
                    ->sortable(),
                IconColumn::make('is_free')
                    ->label('Zadarmo')
                    ->boolean(),
                TextColumn::make('fee_amount')
                    ->label('Suma')
                    ->formatStateUsing(fn ($record): string => number_format((float) $record->fee_amount, 2).' '.$record->fee_currency)
                    ->sortable(),
                TextColumn::make('payment_deadline_at')
                    ->label('Splatnosť')
                    ->dateTime('d.m.Y')
                    ->placeholder('-')
                    ->sortable(),
                TextColumn::make('starts_at')
                    ->label('Začiatok')
                    ->date()
                    ->sortable(),
                TextColumn::make('ends_at')
                    ->label('Koniec')
                    ->date()
                    ->sortable(),
            ])
            ->defaultSort('status')
            ->filters([
                SelectFilter::make('status')
                    ->label('Stav')
                    ->options(MembershipStatusEnum::translations()),
            ])
            ->recordActions([
                Action::make('recordPayment')
                    ->label('Zaznamenať platbu')
                    ->icon('heroicon-o-banknotes')
                    ->visible(fn (Membership $record): bool => $record->status === MembershipStatusEnum::PENDING && ! $record->is_free)
                    ->schema([
                        TextInput::make('amount')
                            ->label('Suma')
                            ->numeric()
                            ->required()
                            ->default(fn (Membership $record): string => (string) $record->fee_amount),
                        Select::make('currency')
                            ->label('Mena')
                            ->options(['EUR' => 'EUR', 'CZK' => 'CZK', 'USD' => 'USD'])
                            ->default(fn (Membership $record): string => $record->fee_currency)
                            ->required(),
                        Select::make('payment_method')
                            ->label('Spôsob platby')
                            ->options(PaymentMethodEnum::translations())
                            ->default(PaymentMethodEnum::CASH->value)
                            ->required(),
                        Textarea::make('notes')
                            ->label('Poznámky')
                            ->rows(2),
                        Toggle::make('notify_customer')
                            ->label('Upozorniť zákazníka?')
                            ->helperText('Pošle e-mail s potvrdením platby.')
                            ->default(true),
                    ])
                    ->action(function (array $data, Membership $record): void {
                        $paymentService = app(PaymentService::class);
                        $paymentService->recordManualPayment(
                            $record->user,
                            $record->team,
                            $record,
                            (float) $data['amount'],
                            $data['currency'],
                            PaymentMethodEnum::from($data['payment_method']),
                            $data['notes'] ?? null,
                            ! empty($data['notify_customer']),
                        );

                        Notification::make()
                            ->title('Platba bola zaznamenaná.')
                            ->success()
                            ->send();
                    }),
                Action::make('markFree')
                    ->label('Označiť zadarmo')
                    ->icon('heroicon-o-gift')
                    ->requiresConfirmation()
                    ->visible(fn (Membership $record): bool => ! $record->is_free && $record->status !== MembershipStatusEnum::ACTIVE)
                    ->action(function (Membership $record): void {
                        app(SeasonService::class)->markMembershipFree($record);

                        Notification::make()
                            ->title('Členstvo bolo označené ako zadarmo.')
                            ->success()
                            ->send();
                    }),
                Action::make('renew')
                    ->label('Obnoviť')
                    ->icon('heroicon-o-arrow-path')
                    ->requiresConfirmation()
                    ->visible(fn (Membership $record): bool => $record->status === MembershipStatusEnum::CANCELLED)
                    ->action(function (Membership $record): void {
                        app(SeasonService::class)->renewMembership($record);

                        Notification::make()
                            ->title('Členstvo bolo obnovené.')
                            ->success()
                            ->send();
                    }),
            ]);
    }
}
