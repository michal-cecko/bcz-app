<?php

namespace App\Filament\Resources\TrainingRegistrations\Tables;

use App\Enums\PaymentStatusEnum;
use App\Enums\RegistrationStatusEnum;
use App\Filament\Resources\TrainingRegistrations\TrainingRegistrationResource;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class TrainingRegistrationsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('registered_at', 'desc')
            ->columns([
                TextColumn::make('training.title')
                    ->label('Tréning')
                    ->searchable()
                    ->limit(30),
                TextColumn::make('user.name')
                    ->label('Používateľ')
                    ->searchable()
                    ->placeholder('Hosť'),
                TextColumn::make('user.email')
                    ->label('E-mail')
                    ->searchable()
                    ->placeholder('-'),
                TextColumn::make('status')
                    ->label('Stav')
                    ->badge(),
                TextColumn::make('payment_status')
                    ->label('Platba')
                    ->badge()
                    ->state(function ($record): string {
                        $payment = $record->payments()->latest()->first();

                        return $payment?->status?->value ?? 'unpaid';
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'unpaid' => 'Nezaplatené',
                        default => PaymentStatusEnum::tryFrom($state)?->getLabel() ?? $state,
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'unpaid' => 'danger',
                        default => PaymentStatusEnum::tryFrom($state)?->getColor() ?? 'gray',
                    }),
                TextColumn::make('registered_at')
                    ->label('Registrovaný')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Stav')
                    ->options(RegistrationStatusEnum::class),
            ])
            ->recordActions([
                ViewAction::make()
                    ->url(fn ($record): string => TrainingRegistrationResource::getUrl('view', ['record' => $record])),
            ]);
    }
}
