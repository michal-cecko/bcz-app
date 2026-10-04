<?php

namespace App\Filament\Widgets;

use App\Enums\MembershipStatusEnum;
use App\Models\Membership;
use App\Models\Payment;
use App\Services\PaymentService;
use App\Services\QrPaymentService;
use App\Services\SeasonService;
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
            ->whereHas('season', fn ($q) => $q->where('ends_at', '>=', today()))
            ->orderByDesc('created_at')
            ->with(['season', 'payments'])
            ->first();

        $season = $team?->currentSeason;

        if (! $membership && $season) {
            $membership = app(SeasonService::class)
                ->findOrCreateMembership($season, auth()->user())
                ?->load(['season', 'payments']);
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

        return app(PaymentService::class)->openPaymentFor($membership, $user, $team);
    }

    #[Computed]
    public function qrCode(): ?string
    {
        $membership = $this->membership;

        if (! $membership || $membership->status !== MembershipStatusEnum::PENDING) {
            return null;
        }

        $team = Filament::getTenant();

        if (! $team?->bank_account_iban) {
            return null;
        }

        $payment = $this->pendingPayment;

        if (! $payment) {
            return null;
        }

        return QrPaymentService::qrPlatba(
            iban: $team->bank_account_iban,
            amount: (float) $payment->amount,
            currency: $payment->currency,
            variableSymbol: $payment->formattedVariableSymbol(),
            recipientName: $team->bank_account_name ?? '',
            note: $membership->getQrPaymentNote(),
        );
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
