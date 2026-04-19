<?php

namespace App\Filament\Resources\EventRegistrations\Tables;

use App\Enums\PaymentStatusEnum;
use App\Enums\RegistrationStatusEnum;
use App\Filament\Resources\EventRegistrations\EventRegistrationResource;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class EventRegistrationsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('registered_at', 'desc')
            ->columns([
                TextColumn::make('event.title')
                    ->label('Podujatie')
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
                TextColumn::make('athleteCategory.name')
                    ->label('Kategória')
                    ->state(fn ($record): ?string => $record->athleteCategory?->getTranslation('name', 'sk'))
                    ->placeholder('-')
                    ->toggleable(isToggledHiddenByDefault: true),
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
                    ->url(fn ($record): string => EventRegistrationResource::getUrl('view', ['record' => $record])),
            ]);
    }
}
