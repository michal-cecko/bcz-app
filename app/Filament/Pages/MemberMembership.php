<?php

namespace App\Filament\Pages;

use App\Enums\MembershipStatusEnum;
use App\Enums\PaymentStatusEnum;
use App\Enums\RoleEnum;
use App\Enums\TrainingPricingTypeEnum;
use App\Models\Membership;
use App\Services\PaymentService;
use Filament\Facades\Filament;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
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
use Illuminate\Support\Collection;
use Livewire\Attributes\Computed;

class MemberMembership extends Page implements HasTable
{
    use InteractsWithTable;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedIdentification;

    protected static ?string $navigationLabel = 'Členstvo';

    protected static ?string $title = 'Členstvo';

    protected static ?int $navigationSort = 4;

    public string $paymentMethod = '';

    public bool $showContinuousDisableModal = false;

    public bool $cancelPendingOnDisable = true;

    public static function getNavigationLabel(): string
    {
        return __('member.membership.title');
    }

    public function getTitle(): string
    {
        return __('member.membership.title');
    }

    public function mount(): void
    {
        $team = Filament::getTenant();
        $enabledMethods = $team?->getEnabledPaymentMethodKeys() ?? [];
        $this->paymentMethod = $enabledMethods[0] ?? 'bank_transfer';
    }

    public function payWithGoPay(): void
    {
        $membership = $this->currentMembership;

        if (! $membership || $membership->status !== MembershipStatusEnum::PENDING) {
            // Try to find active season membership
            $team = Filament::getTenant();
            $season = $team?->currentSeason;

            if (! $season) {
                return;
            }

            // Auto-create membership for the season
            $membership = Membership::create([
                'team_id' => $team->id,
                'user_id' => auth()->id(),
                'team_season_id' => $season->id,
                'status' => MembershipStatusEnum::PENDING,
                'fee_amount' => $season->proratedFee(),
                'fee_currency' => $season->fee_currency ?? 'EUR',
                'is_free' => false,
                'starts_at' => $season->starts_at,
                'ends_at' => $season->ends_at,
            ]);
        }

        $team = Filament::getTenant();
        $user = auth()->user();

        if (! $user || ! $team) {
            return;
        }

        try {
            $paymentService = app(PaymentService::class);
            $result = $paymentService->createGoPayPayment(
                user: $user,
                team: $team,
                payable: $membership,
                amount: (float) $membership->fee_amount,
                currency: $membership->fee_currency ?? 'EUR',
            );

            $this->redirect($result['url']);
        } catch (\Exception $e) {
            Notification::make()
                ->title(__('payments.gopay.failed'))
                ->danger()
                ->send();
        }
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

    #[Computed]
    public function continuousMembershipEnabled(): bool
    {
        $team = Filament::getTenant();
        if (! $team) {
            return false;
        }

        $pivot = auth()->user()?->teams()
            ->where('teams.id', $team->id)
            ->wherePivot('role', RoleEnum::ATHLETE->value)
            ->first()?->pivot;

        return (bool) ($pivot?->continuous_membership ?? false);
    }

    #[Computed]
    public function continuousMembershipLocked(): bool
    {
        $team = Filament::getTenant();
        if (! $team) {
            return false;
        }

        return auth()->user()?->trainingRegistrations()
            ->whereHas('training', fn ($q) => $q
                ->where('team_id', $team->id)
                ->where('pricing_type', TrainingPricingTypeEnum::MEMBERSHIP_REQUIRED)
                ->where('is_active', true)
                ->current()
            )
            ->exists() ?? false;
    }

    /**
     * Pending memberships for this user on the current team (not yet paid, not cancelled).
     *
     * @return Collection<int, Membership>
     */
    #[Computed]
    public function pendingMembershipsForDisable(): Collection
    {
        $team = Filament::getTenant();
        if (! $team) {
            return collect();
        }

        return Membership::query()
            ->where('team_id', $team->id)
            ->where('user_id', auth()->id())
            ->where('status', MembershipStatusEnum::PENDING)
            ->with('season')
            ->orderByDesc('created_at')
            ->get();
    }

    public function requestDisableContinuousMembership(): void
    {
        if ($this->continuousMembershipLocked) {
            return;
        }

        $this->cancelPendingOnDisable = true;
        $this->showContinuousDisableModal = true;
    }

    public function cancelDisableContinuousMembership(): void
    {
        $this->showContinuousDisableModal = false;
    }

    public function enableContinuousMembership(): void
    {
        $this->applyContinuousMembership(true);
    }

    public function confirmDisableContinuousMembership(): void
    {
        if ($this->continuousMembershipLocked) {
            $this->showContinuousDisableModal = false;

            return;
        }

        $cancelPending = $this->cancelPendingOnDisable;
        $pending = $cancelPending ? $this->pendingMembershipsForDisable : collect();

        $this->applyContinuousMembership(false);

        if ($cancelPending && $pending->isNotEmpty()) {
            foreach ($pending as $membership) {
                $membership->update(['status' => MembershipStatusEnum::CANCELLED]);
                $membership->payments()
                    ->where('status', PaymentStatusEnum::PENDING)
                    ->update(['status' => PaymentStatusEnum::CANCELLED->value]);
            }

            unset($this->pendingMembershipsForDisable);
            unset($this->currentMembership);

            Notification::make()
                ->title(__('member.membership.continuous_pending_cancelled_notification', ['count' => $pending->count()]))
                ->success()
                ->send();
        }

        $this->showContinuousDisableModal = false;
    }

    protected function applyContinuousMembership(bool $newValue): void
    {
        $team = Filament::getTenant();
        if (! $team) {
            return;
        }

        $user = auth()->user();

        $hasPivot = $user->teams()
            ->where('teams.id', $team->id)
            ->wherePivot('role', RoleEnum::ATHLETE->value)
            ->exists();

        if ($hasPivot) {
            $user->teams()->updateExistingPivot($team->id, [
                'continuous_membership' => $newValue,
            ]);
        } else {
            $user->teams()->attach($team->id, [
                'role' => RoleEnum::ATHLETE->value,
                'is_active' => true,
                'joined_at' => now(),
                'continuous_membership' => $newValue,
            ]);
        }

        unset($this->continuousMembershipEnabled);

        Notification::make()
            ->title($newValue
                ? __('member.membership.continuous_enabled_notification')
                : __('member.membership.continuous_disabled_notification'))
            ->success()
            ->send();
    }

    public function content(Schema $schema): Schema
    {
        // Teamless users (customer panel) have no tenant to manage a membership
        // against — prompt them to join a team first instead of an empty UI.
        if (! Filament::getTenant()) {
            return $schema->components([
                View::make('filament.components.membership-no-team'),
            ]);
        }

        $membership = $this->currentMembership;
        $continuousEnabled = $this->continuousMembershipEnabled;
        $continuousLocked = $this->continuousMembershipLocked;

        $components = [];

        $components[] = View::make('filament.components.continuous-membership-toggle')
            ->viewData([
                'enabled' => $continuousEnabled,
                'locked' => $continuousLocked,
                'showContinuousDisableModal' => $this->showContinuousDisableModal,
                'pendingMemberships' => $this->pendingMembershipsForDisable,
                'cancelPendingOnDisable' => $this->cancelPendingOnDisable,
            ]);

        if ($membership) {
            $components[] = $this->buildCurrentMembershipSection($membership);
        } else {
            $team = Filament::getTenant();
            $activeSeason = $team?->currentSeason;

            if ($activeSeason) {
                $components[] = Section::make(__('member.membership.current_season_with_name', ['name' => $activeSeason->name]))
                    ->description($activeSeason->starts_at->format('d.m.Y').' - '.$activeSeason->ends_at->format('d.m.Y'))
                    ->schema([
                        TextEntry::make('season_fee')
                            ->label(__('member.membership.season_fee'))
                            ->state(number_format((float) $activeSeason->proratedFee(), 2).' '.($activeSeason->fee_currency ?? 'EUR'))
                            ->helperText(__('member.membership.season_fee_help')),
                        TextEntry::make('season_ends')
                            ->label(__('member.membership.season_ends'))
                            ->state($activeSeason->ends_at->format('d.m.Y')),
                        View::make('filament.components.membership-payment-widget')
                            ->viewData(['season' => $activeSeason, 'paymentMethod' => $this->paymentMethod])
                            ->columnSpanFull(),
                    ])
                    ->columns(2);
            } else {
                $components[] = Section::make(__('member.membership.current_season'))
                    ->schema([
                        EmptyState::make(__('member.membership.no_active_season_heading'))
                            ->description(__('member.membership.no_active_season'))
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
                ->label(__('member.membership.status'))
                ->state($membership->status->getLabel())
                ->badge()
                ->color($membership->status->getColor()),
            TextEntry::make('fee')
                ->label(__('member.membership.fee_label'))
                ->state($membership->is_free ? __('member.membership.is_free_label') : number_format((float) $membership->fee_amount, 2).' '.$membership->fee_currency),
            TextEntry::make('starts_at')
                ->label(__('member.membership.valid_from'))
                ->state($membership->starts_at?->format('d.m.Y') ?? '-'),
            TextEntry::make('ends_at')
                ->label(__('member.membership.valid_to'))
                ->state($membership->ends_at?->format('d.m.Y') ?? '-'),
            IconEntry::make('is_free')
                ->label(__('member.membership.is_free_label'))
                ->state($membership->is_free)
                ->boolean(),
            TextEntry::make('deadline')
                ->label(__('member.membership.deadline'))
                ->state($membership->payment_deadline_at?->format('d.m.Y') ?? '-')
                ->color($membership->status === MembershipStatusEnum::PENDING ? 'warning' : null),
        ];

        // Add inline payment widget for pending memberships
        if ($membership->status === MembershipStatusEnum::PENDING && ! $membership->is_free && $membership->season) {
            $schema[] = View::make('filament.components.membership-payment-widget')
                ->viewData(['season' => $membership->season, 'paymentMethod' => $this->paymentMethod])
                ->columnSpanFull();
        }

        return Section::make(__('member.membership.current_season'))
            ->description($membership->season?->name)
            ->schema($schema)
            ->columns(3);
    }

    public function table(Table $table): Table
    {
        $team = Filament::getTenant();

        return $table
            ->heading(__('member.membership.past_seasons'))
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
                    ->label(__('member.membership.season_label'))
                    ->sortable(),
                TextColumn::make('status')
                    ->label(__('member.membership.status'))
                    ->badge(),
                TextColumn::make('fee_amount')
                    ->label(__('member.membership.fee_label'))
                    ->formatStateUsing(fn (Membership $record): string => $record->is_free
                        ? __('member.membership.is_free_label')
                        : number_format((float) $record->fee_amount, 2).' '.$record->fee_currency
                    ),
                TextColumn::make('starts_at')
                    ->label(__('member.membership.valid_from'))
                    ->date('d.m.Y'),
                TextColumn::make('ends_at')
                    ->label(__('member.membership.valid_to'))
                    ->date('d.m.Y'),
            ])
            ->paginated(false)
            ->emptyStateHeading(__('member.membership.no_past_memberships'));
    }
}
