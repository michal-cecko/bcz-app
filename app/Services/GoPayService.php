<?php

namespace App\Services;

use GoPay\Definition\Language;
use GoPay\Definition\Payment\Currency;
use GoPay\Definition\Payment\PaymentInstrument;
use GoPay\Definition\Payment\Recurrence;
use GoPay\Definition\TokenScope;
use GoPay\Http\Response;
use GoPay\Payments;

class GoPayService
{
    private Payments $gopay;

    public function __construct()
    {
        $gatewayUrl = config('gopay.is_production')
            ? 'https://gate.gopay.cz/api'
            : 'https://gw.sandbox.gopay.com/api';

        $this->gopay = \GoPay\payments([
            'goid' => config('gopay.goid'),
            'clientId' => config('gopay.client_id'),
            'clientSecret' => config('gopay.client_secret'),
            'gatewayUrl' => $gatewayUrl,
            'scope' => TokenScope::ALL,
            'language' => $this->resolveLanguage(),
        ]);
    }

    /**
     * Create a one-time payment and return the GoPay response.
     *
     * @param  array{
     *   amount: int,
     *   currency: string,
     *   order_number: string,
     *   description: string,
     *   payer_email: ?string,
     *   items: array<array{name: string, amount: int}>,
     *   additional_params: ?array<array{name: string, value: string}>,
     * }  $params
     */
    public function createPayment(array $params): Response
    {
        $payment = [
            'payer' => [
                'default_payment_instrument' => PaymentInstrument::PAYMENT_CARD,
                'allowed_payment_instruments' => [PaymentInstrument::PAYMENT_CARD, PaymentInstrument::BANK_ACCOUNT],
                'contact' => [
                    'email' => $params['payer_email'] ?? '',
                ],
            ],
            'target' => [
                'type' => 'ACCOUNT',
                'goid' => config('gopay.goid'),
            ],
            'amount' => $params['amount'],
            'currency' => $this->mapCurrency($params['currency']),
            'order_number' => $params['order_number'],
            'order_description' => $params['description'],
            'items' => $params['items'],
            'callback' => [
                'return_url' => config('gopay.return_url'),
                'notification_url' => config('gopay.notification_url'),
            ],
            'lang' => $this->resolveLanguage(),
        ];

        if (! empty($params['additional_params'])) {
            $payment['additional_params'] = $params['additional_params'];
        }

        return $this->gopay->createPayment($payment);
    }

    /**
     * Create a payment with ON_DEMAND recurrence for subscriptions.
     *
     * @param  array{
     *   amount: int,
     *   currency: string,
     *   order_number: string,
     *   description: string,
     *   payer_email: ?string,
     *   items: array<array{name: string, amount: int}>,
     *   additional_params: ?array<array{name: string, value: string}>,
     * }  $params
     */
    public function createRecurringPayment(array $params): Response
    {
        $payment = [
            'payer' => [
                'default_payment_instrument' => PaymentInstrument::PAYMENT_CARD,
                'allowed_payment_instruments' => [PaymentInstrument::PAYMENT_CARD],
                'contact' => [
                    'email' => $params['payer_email'] ?? '',
                ],
            ],
            'target' => [
                'type' => 'ACCOUNT',
                'goid' => config('gopay.goid'),
            ],
            'amount' => $params['amount'],
            'currency' => $this->mapCurrency($params['currency']),
            'order_number' => $params['order_number'],
            'order_description' => $params['description'],
            'items' => $params['items'],
            'recurrence' => [
                'recurrence_cycle' => Recurrence::ON_DEMAND,
                'recurrence_date_to' => now()->addYears(5)->format('Y-m-d'),
            ],
            'callback' => [
                'return_url' => config('gopay.return_url'),
                'notification_url' => config('gopay.notification_url'),
            ],
            'lang' => $this->resolveLanguage(),
        ];

        if (! empty($params['additional_params'])) {
            $payment['additional_params'] = $params['additional_params'];
        }

        return $this->gopay->createPayment($payment);
    }

    /**
     * Charge a recurring payment using the parent payment ID.
     */
    public function createRecurrence(int $parentPaymentId, array $params): Response
    {
        return $this->gopay->createRecurrence($parentPaymentId, [
            'amount' => $params['amount'],
            'currency' => $this->mapCurrency($params['currency']),
            'order_number' => $params['order_number'],
            'order_description' => $params['description'],
            'items' => $params['items'],
        ]);
    }

    /**
     * Get payment status from GoPay.
     */
    public function getPaymentStatus(int $paymentId): Response
    {
        return $this->gopay->getStatus($paymentId);
    }

    /**
     * Refund a payment (amount in cents).
     */
    public function refundPayment(int $paymentId, int $amountInCents): Response
    {
        return $this->gopay->refundPayment($paymentId, $amountInCents);
    }

    /**
     * Void (cancel) a recurring payment.
     */
    public function voidRecurrence(int $paymentId): Response
    {
        return $this->gopay->voidRecurrence($paymentId);
    }

    private function resolveLanguage(): string
    {
        return match (app()->getLocale()) {
            'cs' => Language::CZECH,
            'en' => Language::ENGLISH,
            default => Language::SLOVAK,
        };
    }

    private function mapCurrency(string $currency): string
    {
        return match (strtoupper($currency)) {
            'CZK' => Currency::CZECH_CROWNS,
            'USD' => Currency::US_DOLLAR,
            'GBP' => Currency::BRITISH_POUND,
            'PLN' => Currency::POLISH_ZLOTY,
            'HUF' => Currency::HUNGARIAN_FORINT,
            default => Currency::EUROS,
        };
    }
}
