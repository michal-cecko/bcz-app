<?php

namespace App\Filament\Pages;

use App\Enums\MembershipStatusEnum;
use App\Models\Membership;
use App\Models\Payment;
use Filament\Facades\Filament;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Pages\Page;
use Filament\Schemas\Components\EmbeddedTable;
use Filament\Schemas\Components\EmptyState;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\View;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Livewire\Attributes\Computed;

class MemberMembership extends Page implements HasTable
{
    use InteractsWithTable;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedIdentification;

    protected static ?string $navigationLabel = 'Členstvo';

    protected static ?string $title = 'Členstvo';

    protected static ?int $navigationSort = 4;

    public string $paymentMethod = '';

    public function mount(): void
    {
        $team = Filament::getTenant();
        $enabledMethods = $team?->payment_methods_enabled ?? ['bank_transfer', 'cash'];
        $this->paymentMethod = $enabledMethods[0] ?? 'bank_transfer';
    }

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()?->isMemberLevel() ?? false;
    }

    #[Computed]
    public function currentMembership(): ?Membership
    {
        $team = Filament::getTenant();

        return Membership::query()
            ->where('team_id', $team?->id)
            ->where('user_id', auth()->id())
            ->whereHas('season', fn ($q) => $q->where('starts_at', '<=', now())->where('ends_at', '>=', now()))
            ->with(['season', 'payments'])
            ->first();
    }

    public function content(Schema $schema): Schema
    {
        $membership = $this->currentMembership;

        $components = [];

        if ($membership) {
            $components[] = $this->buildCurrentMembershipSection($membership);

            if ($membership->payments->isNotEmpty()) {
                $components[] = $this->buildPaymentsSection($membership);
            }
        } else {
            $team = Filament::getTenant();
            $activeSeason = $team?->currentSeason;

            if ($activeSeason) {
                $components[] = Section::make('Aktuálna sezóna: '.$activeSeason->name)
                    ->description($activeSeason->starts_at->format('d.m.Y').' - '.$activeSeason->ends_at->format('d.m.Y'))
                    ->schema([
                        TextEntry::make('season_fee')
                            ->label('Členský poplatok')
                            ->state(number_format((float) $activeSeason->proratedFee(), 2).' '.($activeSeason->fee_currency ?? 'EUR'))
                            ->helperText('Pomerná čiastka za zostávajúce obdobie sezóny'),
                        TextEntry::make('season_ends')
                            ->label('Sezóna končí')
                            ->state($activeSeason->ends_at->format('d.m.Y')),
                        View::make('filament.components.membership-payment-widget')
                            ->viewData(['season' => $activeSeason, 'paymentMethod' => $this->paymentMethod])
                            ->columnSpanFull(),
                    ])
                    ->columns(2);
            } else {
                $components[] = Section::make('Aktuálna sezóna')
                    ->schema([
                        EmptyState::make('Žiadna aktívna sezóna')
                            ->description('V tomto tíme momentálne nie je aktívna sezóna.')
                            ->icon(Heroicon::OutlinedIdentification),
                    ]);
            }
        }

        $components[] = EmbeddedTable::make();

        return $schema->components($components);
    }

    private function buildCurrentMembershipSection(Membership $membership): Section
    {
        $schema = [
            TextEntry::make('status')
                ->label('Stav')
                ->state($membership->status->getLabel())
                ->badge()
                ->color($membership->status->getColor()),
            TextEntry::make('fee')
                ->label('Poplatok')
                ->state($membership->is_free ? 'Zadarmo' : number_format((float) $membership->fee_amount, 2).' '.$membership->fee_currency),
            TextEntry::make('starts_at')
                ->label('Platné od')
                ->state($membership->starts_at?->format('d.m.Y') ?? '-'),
            TextEntry::make('ends_at')
                ->label('Platné do')
                ->state($membership->ends_at?->format('d.m.Y') ?? '-'),
            IconEntry::make('is_free')
                ->label('Zadarmo')
                ->state($membership->is_free)
                ->boolean(),
            TextEntry::make('deadline')
                ->label('Splatnosť')
                ->state($membership->payment_deadline_at?->format('d.m.Y') ?? '-')
                ->color($membership->status === MembershipStatusEnum::PENDING ? 'warning' : null),
        ];

        // Add inline payment widget for pending memberships
        if ($membership->status === MembershipStatusEnum::PENDING && ! $membership->is_free && $membership->season) {
            $schema[] = View::make('filament.components.membership-payment-widget')
                ->viewData(['season' => $membership->season, 'paymentMethod' => $this->paymentMethod])
                ->columnSpanFull();
        }

        return Section::make('Aktuálna sezóna')
            ->description($membership->season?->name)
            ->schema($schema)
            ->columns(3);
    }

    private function buildPaymentsSection(Membership $membership): Section
    {
        return Section::make('Platby za aktuálne členstvo')
            ->schema(
                $membership->payments->map(fn (Payment $payment, int $index) => TextEntry::make("payment_{$index}")
                    ->label($payment->paid_at?->format('d.m.Y') ?? $payment->created_at?->format('d.m.Y'))
                    ->state(number_format((float) $payment->amount, 2).' '.$payment->currency)
                    ->badge()
                    ->color($payment->status->getColor())
                )->toArray()
            )
            ->columns(3)
            ->collapsible();
    }

    public function table(Table $table): Table
    {
        $team = Filament::getTenant();

        return $table
            ->heading('Minulé sezóny')
            ->query(
                Membership::query()
                    ->where('team_id', $team?->id)
                    ->where('user_id', auth()->id())
                    ->whereHas('season', fn ($q) => $q->where('ends_at', '<', now()))
                    ->with('season')
                    ->orderByDesc('created_at')
            )
            ->columns([
                TextColumn::make('season.name')
                    ->label('Sezóna')
                    ->sortable(),
                TextColumn::make('status')
                    ->label('Stav')
                    ->badge(),
                TextColumn::make('fee_amount')
                    ->label('Poplatok')
                    ->formatStateUsing(fn (Membership $record): string => $record->is_free
                        ? 'Zadarmo'
                        : number_format((float) $record->fee_amount, 2).' '.$record->fee_currency
                    ),
                TextColumn::make('starts_at')
                    ->label('Od')
                    ->date('d.m.Y'),
                TextColumn::make('ends_at')
                    ->label('Do')
                    ->date('d.m.Y'),
            ])
            ->paginated(false)
            ->emptyStateHeading('Žiadne minulé členstvá')
            ->emptyStateDescription('Zatiaľ nemáte žiadnu históriu členstva.');
    }
}
