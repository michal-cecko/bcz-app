<?php

namespace App\Filament\Widgets;

use App\Filament\Pages\MemberPayments;
use App\Models\Payment;
use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;

class RecentPaymentsWidget extends TableWidget
{
    protected int|string|array $columnSpan = 1;

    protected static ?int $sort = 3;

    protected static ?string $heading = 'Posledné platby';

    public function table(Table $table): Table
    {
        $team = Filament::getTenant();

        return $table
            ->headerActions([
                Action::make('viewAll')
                    ->label('Všetky moje platby')
                    ->button()
                    ->color('gray')
                    ->url(MemberPayments::getUrl())
                    ->size('sm'),
            ])
            ->query(
                Payment::query()
                    ->where('user_id', auth()->id())
                    ->where('team_id', $team?->id)
                    ->with('payable')
                    ->orderByDesc('created_at')
                    ->limit(5)
            )
            ->columns([
                TextColumn::make('payable_name')
                    ->label('Predmet')
                    ->state(fn (Payment $record): string => $record->payable_name),
                TextColumn::make('amount')
                    ->label('Suma')
                    ->formatStateUsing(fn (Payment $record): string => number_format((float) $record->amount, 2).' '.$record->currency),
                TextColumn::make('status')
                    ->label('Stav')
                    ->badge(),
                TextColumn::make('paid_at')
                    ->label('Dátum')
                    ->date('d.m.Y')
                    ->placeholder('-'),
            ])
            ->recordActions([
                Action::make('view')
                    ->icon(Heroicon::Eye)
                    ->color('gray')
                    ->iconButton()
                    ->modalHeading('Detail platby')
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Zavrieť')
                    ->schema([
                        Section::make('Platba')
                            ->schema([
                                TextEntry::make('payable_name')
                                    ->label('Predmet')
                                    ->state(fn (Payment $record): string => $record->payable_name),
                                TextEntry::make('payable_type')
                                    ->label('Typ')
                                    ->formatStateUsing(fn (string $state): string => match ($state) {
                                        'membership' => 'Členstvo',
                                        'training_registration' => 'Tréning',
                                        'competition_registration' => 'Súťaž',
                                        'event_registration' => 'Podujatie',
                                        default => $state,
                                    }),
                                TextEntry::make('amount')
                                    ->label('Suma')
                                    ->formatStateUsing(fn (Payment $record): string => number_format((float) $record->amount, 2).' '.$record->currency),
                                TextEntry::make('status')
                                    ->label('Stav')
                                    ->badge(),
                                TextEntry::make('payment_method')
                                    ->label('Spôsob platby')
                                    ->badge(),
                                TextEntry::make('paid_at')
                                    ->label('Zaplatené')
                                    ->dateTime('d.m.Y H:i')
                                    ->placeholder('-'),
                            ])
                            ->columns(2),
                    ]),
            ])
            ->paginated(false)
            ->emptyStateHeading('Žiadne platby')
            ->emptyStateDescription('Zatiaľ žiadne platby.');
    }
}
