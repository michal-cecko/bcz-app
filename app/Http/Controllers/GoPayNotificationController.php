<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\TeamSubscription;
use App\Services\GoPayService;
use App\Services\PaymentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GoPayNotificationController extends Controller
{
    public function handle(Request $request, GoPayService $goPayService, PaymentService $paymentService): JsonResponse
    {
        $paymentId = (int) $request->query('id');

        if (! $paymentId) {
            return response()->json(['error' => 'Missing payment ID'], 400);
        }

        // Try to handle as a regular payment
        $payment = Payment::where('gopay_payment_id', (string) $paymentId)->first();
        if ($payment) {
            $paymentService->handleGoPayNotification($paymentId);

            return response()->json(['status' => 'ok']);
        }

        // Try to handle as a subscription parent payment
        $subscription = TeamSubscription::where('gopay_parent_payment_id', (string) $paymentId)->first();
        if ($subscription) {
            $response = $goPayService->getPaymentStatus($paymentId);

            if ($response->hasSucceed()) {
                $state = $response->json['state'] ?? null;

                if ($state === 'PAID') {
                    // Find the pending payment for this subscription
                    $pendingPayment = Payment::query()
                        ->where('gopay_payment_id', (string) $paymentId)
                        ->where('payable_type', 'team_subscription')
                        ->first();

                    if ($pendingPayment) {
                        $pendingPayment->update([
                            'status' => 'completed',
                            'paid_at' => now(),
                        ]);
                    }
                }
            }

            return response()->json(['status' => 'ok']);
        }

        return response()->json(['status' => 'ignored']);
    }
}
