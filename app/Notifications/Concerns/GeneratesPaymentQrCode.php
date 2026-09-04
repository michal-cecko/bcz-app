<?php

namespace App\Notifications\Concerns;

use App\Models\Payment;
use App\Services\QrPaymentService;

/**
 * Resolves the raw PNG bytes for a payment's QR code, for inline (CID)
 * embedding into transactional emails via `$message->embedData()`.
 *
 * QrPaymentService::generateQrForPayment() returns a base64-encoded PNG (the
 * format consumed directly as a `data:` URI by the /platba/{payment} web
 * page). Transactional email needs the raw bytes instead, since `embedData()`
 * attaches them as an inline MIME part rather than a base64 string.
 */
trait GeneratesPaymentQrCode
{
    /**
     * @return string|null Raw PNG bytes, or null when the payable has no
     *                     configured IBAN (bank transfer disabled for the team).
     */
    protected function qrCodeImageForPayment(?Payment $payment): ?string
    {
        if (! $payment) {
            return null;
        }

        $base64 = app(QrPaymentService::class)->generateQrForPayment($payment);

        if (! $base64) {
            return null;
        }

        return base64_decode($base64);
    }
}
