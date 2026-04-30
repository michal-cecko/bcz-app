<?php

namespace App\Filament\Widgets;

use App\Enums\MembershipStatusEnum;
use App\Models\Membership;
use App\Models\Payment;
use App\Services\PaymentService;
use App\Services\QrPaymentService;
use Filament\Facades\Filament;
use Filament\Notifications\Notification;
use Filament\Widgets\Widget;
use Livewire\Attributes\Computed;

class MembershipStatusWidget extends Widget
{
    protected string $view = 'filament.widgets.membership-status-widget';

    protected int|string|array $columnSpan = 'full';

    public function getColumnSpan(): int|string|array
    {
        $membership = $this->membership;

        if (! $membership) {
            $team = Filament::getTenant();

            return $team?->currentSeason ? 'full' : 1;
        }

        if ($membership->status === MembershipStatusEnum::PENDING && ! $membership->is_free) {
            return 'full';
        }

        return 1;
    }

    protected static ?int $sort = 1;

    public string $paymentMethod = '';

    public function mount(): void
    {
        $team = Filament::getTenant();
        $enabledMethods = $team?->getEnabledPaymentMethodKeys() ?? [];
        $this->paymentMethod = $enabledMethods[0] ?? 'bank_transfer';

        // Show success notification when returning from GoPay
        if (session('gopay_payment_success')) {
            Notification::make()
                ->title(__('payments.gopay.success_title'))
                ->body(__('payments.gopay.success_body'))
                ->success()
                ->send();
        }
    }

    public function payWithGoPay(): void
    {
        $membership = $this->membership;

        if (! $membership || $membership->status !== MembershipStatusEnum::PENDING) {
            return;
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

    #[Computed]
    public function membership(): ?Membership
    {
        $team = Filament::getTenant();

        $membership = Membership::query()
            ->where('team_id', $team?->id)
            ->where('user_id', auth()->id())
            ->whereHas('season', fn ($q) => $q->where('ends_at', '>=', now()))
            ->orderByDesc('created_at')
            ->with(['season', 'payments'])
            ->first();

        // Auto-create PENDING membership if none exists and active season is available
        if (! $membership) {
            $season = $team?->currentSeason;

            if ($season) {
                $membership = Membership::create([
                    'team_id' => $team->id,
                    'user_id' => auth()->id(),
                    'team_season_id' => $season->id,
                    'status' => MembershipStatusEnum::PENDING,
                    'fee_amount' => $season->proratedFee(),
                    'fee_currency' => $season->fee_currency ?? 'EUR',
                    'is_free' => false,
                    'payment_deadline_at' => now()->addDays($season->payment_deadline_days ?? 14),
                    'starts_at' => $season->starts_at,
                    'ends_at' => $season->ends_at,
                ]);

                $membership->load(['season', 'payments']);
            }
        }

        return $membership;
    }

    #[Computed]
    public function pendingPayment(): ?Payment
    {
        $membership = $this->membership;

        if (! $membership || $membership->status !== MembershipStatusEnum::PENDING || $membership->is_free) {
            return null;
        }

        $team = Filament::getTenant();
        $user = auth()->user();

        if (! $team || ! $user) {
            return null;
        }

        return app(PaymentService::class)->ensurePendingPaymentFor(
            user: $user,
            team: $team,
            payable: $membership,
            amount: (float) $membership->fee_amount,
            currency: $membership->fee_currency ?? 'EUR',
        );
    }

    #[Computed]
    public function qrCodes(): array
    {
        $membership = $this->membership;

        if (! $membership || $membership->status !== MembershipStatusEnum::PENDING) {
            return [];
        }

        $team = Filament::getTenant();

        if (! $team?->bank_account_iban) {
            return [];
        }

        $args = [
            'iban' => $team->bank_account_iban,
            'amount' => (float) $membership->fee_amount,
            'currency' => $membership->fee_currency ?? 'EUR',
            'variableSymbol' => $this->pendingPayment?->formattedVariableSymbol() ?? '',
            'recipientName' => $team->bank_account_name ?? '',
        ];

        return [
            'sk' => QrPaymentService::payBySquare(...$args),
            'cz' => QrPaymentService::qrPlatba(...$args),
        ];
    }

    #[Computed]
    public function teamSettings(): array
    {
        $team = Filament::getTenant();
        if (! $team) {
            return [];
        }

        return [
            'iban' => $team->bank_account_iban,
            'bank_name' => $team->bank_account_name,
        ];
    }
}
