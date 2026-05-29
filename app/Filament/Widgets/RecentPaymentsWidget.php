<?php

namespace App\Filament\Widgets;

use App\Enums\PaymentStatusEnum;
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
use Illuminate\Support\Facades\URL;

class RecentPaymentsWidget extends TableWidget
{
    protected int|string|array $columnSpan = 1;

    protected static ?int $sort = 3;

    protected static ?string $heading = 'Posledné platby';

    public function getHeading(): string
    {
        return __('member.payments.recent');
    }

    public function table(Table $table): Table
    {
        $team = Filament::getTenant();

        return $table
            ->headerActions([
                Action::make('viewAll')
                    ->label(__('member.payments.all_my_payments'))
                    ->button()
                    ->color('gray')
                    ->url(MemberPayments::getUrl())
                    ->size('sm'),
            ])
            ->query(
                Payment::query()
                    ->where('user_id', auth()->id())
                    // Customer panel is tenant-free: scoping to a null team_id
                    // left the dashboard "recent payments" empty even though the
                    // customer has payments. Only filter by team in the admin panel.
                    ->when($team, fn ($query) => $query->where('team_id', $team->id))
                    ->with('payable')
                    ->orderByDesc('created_at')
                    ->limit(5)
            )
            ->columns([
                TextColumn::make('payable_name')
                    ->label(__('member.payments.subject'))
                    ->state(fn (Payment $record): string => $record->payable_name),
                TextColumn::make('amount')
                    ->label(__('member.payments.amount'))
                    ->formatStateUsing(fn (Payment $record): string => number_format((float) $record->amount, 2).' '.$record->currency),
                TextColumn::make('status')
                    ->label(__('member.payments.status'))
                    ->badge(),
                TextColumn::make('paid_at')
                    ->label(__('member.payments.paid_at'))
                    ->date('d.m.Y')
                    ->placeholder('-'),
            ])
            ->recordActions([
                Action::make('openPaymentPage')
                    ->icon(Heroicon::ArrowTopRightOnSquare)
                    ->color('primary')
                    ->iconButton()
                    ->tooltip(__('payments.open_payment_page'))
                    ->visible(fn (Payment $record): bool => $record->status === PaymentStatusEnum::PENDING)
                    ->url(fn (Payment $record): string => URL::signedRoute('payment.page', ['payment' => $record->id]))
                    ->openUrlInNewTab(),
                Action::make('view')
                    ->icon(Heroicon::Eye)
                    ->color('gray')
                    ->iconButton()
                    ->modalHeading(__('member.payments.detail_modal'))
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel(__('member.payments.modal_close'))
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
                                TextEntry::make('paid_at')
                                    ->label(__('member.payments.paid'))
                                    ->dateTime('d.m.Y H:i')
                                    ->placeholder('-'),
                            ])
                            ->columns(2),
                    ]),
            ])
            ->paginated(false)
            ->emptyStateHeading(__('member.payments.recent_empty_heading'))
            ->emptyStateDescription(__('member.payments.recent_empty_description'));
    }
}
