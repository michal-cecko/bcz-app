<?php

namespace App\Filament\Widgets;

use App\Enums\MembershipStatusEnum;
use App\Models\Membership;
use App\Services\QrPaymentService;
use Filament\Facades\Filament;
use Filament\Widgets\Widget;
use Livewire\Attributes\Computed;

class MembershipStatusWidget extends Widget
{
    protected string $view = 'filament.widgets.membership-status-widget';

    protected int|string|array $columnSpan = 'full';

    public function getColumnSpan(): int|string|array
    {
        $membership = $this->membership;

        if ($membership && $membership->status === MembershipStatusEnum::PENDING && ! $membership->is_free) {
            return 'full';
        }

        return 1;
    }

    protected static ?int $sort = 1;

    public string $paymentMethod = 'stripe';

    #[Computed]
    public function membership(): ?Membership
    {
        $team = Filament::getTenant();

        return Membership::query()
            ->where('team_id', $team?->id)
            ->where('user_id', auth()->id())
            ->orderByDesc('created_at')
            ->with(['season', 'payments'])
            ->first();
    }

    #[Computed]
    public function qrCodes(): array
    {
        $membership = $this->membership;

        if (! $membership || $membership->status !== MembershipStatusEnum::PENDING) {
            return [];
        }

        $payment = $membership->payments()->latest()->first();
        if (! $payment) {
            return [];
        }

        $qrService = app(QrPaymentService::class);

        return [
            'sk' => $qrService->generatePayBySquareForPayment($payment),
            'cz' => $qrService->generateQrPlatbaForPayment($payment),
        ];
    }

    #[Computed]
    public function teamSettings(): array
    {
        $team = Filament::getTenant();
        if (! $team) {
            return [];
        }

        $settings = $team->settings ?? [];

        return [
            'iban' => $settings['iban'] ?? null,
            'bank_name' => $settings['bank_name'] ?? null,
        ];
    }
}
