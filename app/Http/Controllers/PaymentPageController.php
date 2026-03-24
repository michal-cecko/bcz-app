<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use Illuminate\Contracts\View\View;

class PaymentPageController extends Controller
{
    public function __invoke(Payment $payment): View
    {
        $payment->load(['team', 'payable', 'user']);

        return view('pages.payment', [
            'payment' => $payment,
        ]);
    }
}
