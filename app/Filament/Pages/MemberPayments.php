<?php

namespace App\Filament\Pages;

use App\Enums\PaymentMethodEnum;
use App\Enums\PaymentStatusEnum;
use App\Models\Payment;
use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Infolists\Components\TextEntry;
use Filament\Pages\Page;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\URL;

class MemberPayments extends Page implements HasTable
{
    use InteractsWithTable;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedBanknotes;

    protected static ?string $navigationLabel = 'Platby';

    protected static ?string $title = 'Platby';

    protected static ?int $navigationSort = 5;

    protected string $view = 'filament.pages.member-payments';

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()?->isMemberLevel() ?? false;
    }

    public function table(Table $table): Table
    {
        $team = Filament::getTenant();

        return $table
            ->query(
                Payment::query()
                    ->where('user_id', auth()->id())
                    ->where('team_id', $team?->id)
                    ->with('payable')
            )
            ->columns([
                TextColumn::make('payable_name')
                    ->label('Predmet')
                    ->state(fn (Payment $record): string => $record->payable_name),
                TextColumn::make('payable_type')
                    ->label('Typ')
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'membership' => 'Členstvo',
                        'training_registration' => 'Tréning',
                        'competition_registration' => 'Súťaž',
                        'event_registration' => 'Podujatie',
                        'team_subscription' => 'Predplatné',
                        default => $state,
                    })
                    ->badge(),
                TextColumn::make('amount')
                    ->label('Suma')
                    ->formatStateUsing(fn (Payment $record): string => number_format((float) $record->amount, 2).' '.$record->currency)
                    ->sortable(),
                TextColumn::make('payment_method')
                    ->label('Spôsob')
                    ->badge()
                    ->sortable(),
                TextColumn::make('status')
                    ->label('Stav')
                    ->badge()
                    ->sortable(),
                TextColumn::make('paid_at')
                    ->label('Dátum')
                    ->dateTime('d.m.Y')
                    ->placeholder('-')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('status')
                    ->label('Stav')
                    ->options(PaymentStatusEnum::translations()),
                SelectFilter::make('payment_method')
                    ->label('Spôsob')
                    ->options(PaymentMethodEnum::translations()),
            ])
            ->recordActions([
                Action::make('openPaymentPage')
                    ->label('Otvoriť platbu')
                    ->icon(Heroicon::ArrowTopRightOnSquare)
                    ->color('primary')
                    ->url(fn (Payment $record): string => URL::signedRoute('payment.page', ['payment' => $record->id]))
                    ->openUrlInNewTab(),
                Action::make('view')
                    ->label('Detail')
                    ->icon(Heroicon::Eye)
                    ->color('gray')
                    ->modalHeading('Detail platby')
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Zavrieť')
                    ->schema([
                        Grid::make(2)
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
                                        TextEntry::make('variable_symbol')
                                            ->label('Variabilný symbol')
                                            ->placeholder('-'),
                                    ])
                                    ->columns(2),
                                Section::make('Dátumy')
                                    ->schema([
                                        TextEntry::make('paid_at')
                                            ->label('Zaplatené')
                                            ->dateTime('d.m.Y H:i')
                                            ->placeholder('-'),
                                        TextEntry::make('refunded_at')
                                            ->label('Vrátené')
                                            ->dateTime('d.m.Y H:i')
                                            ->placeholder('-'),
                                        TextEntry::make('created_at')
                                            ->label('Vytvorené')
                                            ->dateTime('d.m.Y H:i'),
                                    ]),
                            ]),
                        Section::make('Poznámky')
                            ->schema([
                                TextEntry::make('notes')
                                    ->hiddenLabel()
                                    ->placeholder('Žiadne poznámky'),
                            ])
                            ->collapsible()
                            ->collapsed(),
                    ]),
            ])
            ->emptyStateHeading('Zatiaľ žiadne platby')
            ->emptyStateDescription('Po uskutočnení platby sa tu zobrazia.');
    }
}
