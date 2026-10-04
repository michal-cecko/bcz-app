<?php

namespace App\Observers;

use App\Enums\PaymentStatusEnum;
use App\Models\Payment;
use App\Services\PaymentService;

/**
 * A completed payment that is refunded, changed or deleted may no longer cover
 * the price, so the registration or membership it paid for is checked again.
 */
class PaymentObserver
{
    /**
     * In "updated" the original still holds the value from before the save.
     */
    public function updated(Payment $payment): void
    {
        if ($payment->getOriginal('status') !== PaymentStatusEnum::COMPLETED
            || ! $payment->wasChanged(['status', 'amount', 'currency'])) {
            return;
        }

        app(PaymentService::class)->reopenIfNoLongerPaid($payment->payable);
    }

    public function deleted(Payment $payment): void
    {
        if ($payment->status !== PaymentStatusEnum::COMPLETED) {
            return;
        }

        app(PaymentService::class)->reopenIfNoLongerPaid($payment->payable);
    }
}
