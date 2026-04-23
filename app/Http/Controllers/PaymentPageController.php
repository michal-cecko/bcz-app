<?php

namespace App\Http\Controllers;

use App\Enums\PaymentStatusEnum;
use App\Models\Payment;
use Illuminate\Contracts\View\View;

class PaymentPageController extends Controller
{
    public function __invoke(Payment $payment): View
    {
        abort_if($payment->status === PaymentStatusEnum::CANCELLED, 404);

        $payment->load(['team', 'payable', 'user']);

        return view('pages.payment', [
            'payment' => $payment,
        ]);
    }
}
