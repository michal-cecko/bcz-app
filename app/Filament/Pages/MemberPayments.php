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

    public static function getNavigationLabel(): string
    {
        return __('member.payments.title');
    }

    public function getTitle(): string
    {
        return __('member.payments.title');
    }

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
                    ->label(__('member.payments.subject'))
                    ->state(fn (Payment $record): string => $record->payable_name),
                TextColumn::make('payable_type')
                    ->label(__('member.payments.type'))
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
                    ->label(__('member.payments.amount'))
                    ->formatStateUsing(fn (Payment $record): string => number_format((float) $record->amount, 2).' '.$record->currency)
                    ->sortable(),
                TextColumn::make('payment_method')
                    ->label(__('member.payments.method'))
                    ->badge()
                    ->sortable(),
                TextColumn::make('status')
                    ->label(__('member.payments.status'))
                    ->badge()
                    ->sortable(),
                TextColumn::make('paid_at')
                    ->label(__('member.payments.paid_at'))
                    ->dateTime('d.m.Y')
                    ->placeholder('-')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('status')
                    ->label(__('member.payments.status'))
                    ->options(PaymentStatusEnum::translations()),
                SelectFilter::make('payment_method')
                    ->label(__('member.payments.method'))
                    ->options(PaymentMethodEnum::translations()),
            ])
            ->recordActions([
                Action::make('openPaymentPage')
                    ->label(__('payments.open_payment_page'))
                    ->icon(Heroicon::ArrowTopRightOnSquare)
                    ->color('primary')
                    ->visible(fn (Payment $record): bool => $record->status === PaymentStatusEnum::PENDING)
                    ->url(fn (Payment $record): string => URL::signedRoute('payment.page', ['payment' => $record->id]))
                    ->openUrlInNewTab(),
                Action::make('view')
                    ->label(__('member.payments.detail'))
                    ->icon(Heroicon::Eye)
                    ->color('gray')
                    ->modalHeading(__('member.payments.detail_modal'))
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel(__('member.payments.modal_close'))
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Section::make(__('member.events.payment'))
                                    ->schema([
                                        TextEntry::make('payable_name')
                                            ->label(__('member.payments.subject'))
                                            ->state(fn (Payment $record): string => $record->payable_name),
                                        TextEntry::make('payable_type')
                                            ->label(__('member.payments.type'))
                                            ->formatStateUsing(fn (string $state): string => match ($state) {
                                                'membership' => 'Členstvo',
                                                'training_registration' => 'Tréning',
                                                'competition_registration' => 'Súťaž',
                                                'event_registration' => 'Podujatie',
                                                default => $state,
                                            }),
                                        TextEntry::make('amount')
                                            ->label(__('member.payments.amount'))
                                            ->formatStateUsing(fn (Payment $record): string => number_format((float) $record->amount, 2).' '.$record->currency),
                                        TextEntry::make('status')
                                            ->label(__('member.payments.status'))
                                            ->badge(),
                                        TextEntry::make('payment_method')
                                            ->label(__('member.payments.method'))
                                            ->badge(),
                                        TextEntry::make('variable_symbol')
                                            ->label(__('member.events.variable_symbol'))
                                            ->placeholder('-'),
                                    ])
                                    ->columns(2),
                                Section::make(__('member.payments.dates'))
                                    ->schema([
                                        TextEntry::make('paid_at')
                                            ->label(__('member.payments.paid'))
                                            ->dateTime('d.m.Y H:i')
                                            ->placeholder('-'),
                                        TextEntry::make('refunded_at')
                                            ->label(__('member.payments.refunded'))
                                            ->dateTime('d.m.Y H:i')
                                            ->placeholder('-'),
                                        TextEntry::make('created_at')
                                            ->label(__('member.payments.created'))
                                            ->dateTime('d.m.Y H:i'),
                                    ]),
                            ]),
                        Section::make(__('member.payments.notes'))
                            ->schema([
                                TextEntry::make('notes')
                                    ->hiddenLabel()
                                    ->placeholder(__('member.payments.no_notes')),
                            ])
                            ->collapsible()
                            ->collapsed(),
                    ]),
            ])
            ->emptyStateHeading(__('member.payments.empty_heading'))
            ->emptyStateDescription(__('member.payments.empty_description'));
    }
}
