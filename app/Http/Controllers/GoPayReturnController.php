<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Services\GoPayService;
use App\Services\PaymentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class GoPayReturnController extends Controller
{
    public function handle(Request $request, GoPayService $goPayService, PaymentService $paymentService): RedirectResponse
    {
        $paymentId = (int) $request->query('id');

        if (! $paymentId) {
            return redirect('/')->with('error', 'Neplatná platba.');
        }

        // Check the payment status with GoPay
        $response = $goPayService->getPaymentStatus($paymentId);
        $state = $response->hasSucceed() ? ($response->json['state'] ?? 'UNKNOWN') : 'UNKNOWN';

        // Process the notification to update our records
        $paymentService->handleGoPayNotification($paymentId);

        // Find the local payment to determine where to redirect
        $payment = Payment::where('gopay_payment_id', (string) $paymentId)->first();

        if (! $payment) {
            return redirect('/')->with('error', 'Platba nenájdená.');
        }

        // Determine redirect target based on payable type
        $redirectUrl = $this->resolveRedirectUrl($payment, $state);

        if ($state === 'PAID') {
            return redirect($redirectUrl)->with('gopay_payment_success', true);
        }

        if (in_array($state, ['CANCELED', 'TIMEOUTED'])) {
            return redirect($redirectUrl)->with('error', 'Platba bola zrušená alebo vypršala.');
        }

        return redirect($redirectUrl)->with('info', 'Platba sa spracováva.');
    }

    private function resolveRedirectUrl(Payment $payment, string $state): string
    {
        // Membership payments from dashboard → redirect to dashboard
        if ($payment->payable_type === 'membership') {
            return filament()->getUrl();
        }

        // Training registration → redirect to training detail page
        if ($payment->payable_type === 'training_registration') {
            $registration = $payment->payable;

            if ($registration?->training) {
                $training = $registration->training;
                $team = $training->team;

                if ($team) {
                    return '/timy/'.$team->slug.'/treningy/'.$training->slug
                        .($state === 'PAID' ? '?payment=success' : '');
                }
            }
        }

        return '/';
    }
}
