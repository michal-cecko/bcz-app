<?php

namespace App\Filament\Widgets;

use App\Models\Payment;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;

class RecentPaymentsWidget extends TableWidget
{
    protected int|string|array $columnSpan = 'full';

    protected static ?int $sort = 4;

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Payment::query()
                    ->where('user_id', auth()->id())
                    ->orderByDesc('created_at')
                    ->limit(5)
            )
            ->heading('Posledne platby')
            ->columns([
                TextColumn::make('payable_type')
                    ->label('Typ')
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'App\\Models\\Membership' => 'Clenstvo',
                        'App\\Models\\TrainingRegistration' => 'Trening',
                        'App\\Models\\EventRegistration' => 'Podujatie',
                        default => $state,
                    }),
                TextColumn::make('amount')
                    ->label('Suma')
                    ->formatStateUsing(fn ($record): string => number_format((float) $record->amount, 2).' '.$record->currency),
                TextColumn::make('status')
                    ->label('Stav')
                    ->badge(),
                TextColumn::make('paid_at')
                    ->label('Zaplatene')
                    ->dateTime('d.m.Y H:i')
                    ->placeholder('-'),
            ])
            ->paginated(false);
    }
}
