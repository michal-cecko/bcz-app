<?php

namespace App\Livewire;

use App\Contracts\Payable;
use App\Enums\PaymentMethodEnum;
use App\Enums\PaymentStatusEnum;
use App\Models\EventRegistration;
use App\Models\Payment;
use App\Models\TrainingRegistration;
use App\Services\GoPayService;
use App\Services\PaymentService;
use App\Services\QrPaymentService;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class PaymentPage extends Component
{
    public Payment $payment;

    public string $selectedMethod = '';

    public bool $isCompleted = false;

    public bool $isProcessing = false;

    public ?string $errorMessage = null;

    public ?string $qrCodeImage = null;

    public function mount(Payment $payment): void
    {
        $this->payment = $payment->load(['team', 'payable', 'user']);

        $this->isCompleted = in_array($this->payment->status, [
            PaymentStatusEnum::COMPLETED,
            PaymentStatusEnum::REFUNDED,
        ]);

        if (! $this->isCompleted) {
            $enabledMethods = $this->payment->team?->getEnabledPaymentMethodKeys() ?? [];

            if (count($enabledMethods) > 0) {
                $this->selectedMethod = $enabledMethods[0];

                if ($this->selectedMethod === 'bank_transfer') {
                    $this->ensureBankTransferDetails();
                    $this->generateQrCode();
                }
            }
        }
    }

    public function selectMethod(string $method): void
    {
        $this->selectedMethod = $method;
        $this->errorMessage = null;
        $this->qrCodeImage = null;

        if ($method === 'bank_transfer') {
            $this->ensureBankTransferDetails();
            $this->generateQrCode();
        }
    }

    public function pay(): void
    {
        if ($this->isCompleted || $this->isProcessing) {
            return;
        }

        $this->isProcessing = true;
        $this->errorMessage = null;

        try {
            if ($this->selectedMethod === 'gopay') {
                $this->handleGoPayPayment();
            } elseif ($this->selectedMethod === 'bank_transfer') {
                $this->handleBankTransfer();
            } elseif ($this->selectedMethod === 'cash') {
                $this->handleCashPayment();
            }
        } catch (\Throwable $e) {
            $this->errorMessage = 'Nastala chyba pri spracovani platby. Skuste to znova.';
            $this->isProcessing = false;
        }
    }

    protected function handleGoPayPayment(): void
    {
        $payment = $this->payment;
        $goPayService = app(GoPayService::class);

        $orderNumber = strtoupper(substr(class_basename($payment->payable), 0, 3)).'-'.now()->format('ymd').'-'.random_int(1000, 9999);
        $description = $payment->payable instanceof Payable
            ? $payment->payable->getPaymentDescription()
            : class_basename($payment->payable).' #'.$payment->payable?->getKey();

        $response = $goPayService->createPayment([
            'amount' => (int) round((float) $payment->amount * 100),
            'currency' => $payment->currency,
            'order_number' => substr($orderNumber, 0, 128),
            'description' => substr($description, 0, 256),
            'payer_email' => $payment->payer_email,
            'items' => [[
                'name' => substr($description, 0, 256),
                'amount' => (int) round((float) $payment->amount * 100),
            ]],
            'additional_params' => [
                ['name' => 'team_id', 'value' => (string) $payment->team_id],
                ['name' => 'payable_type', 'value' => $payment->payable_type],
                ['name' => 'payable_id', 'value' => (string) $payment->payable_id],
            ],
        ]);

        if ($response->hasSucceed()) {
            $payment->update([
                'payment_method' => PaymentMethodEnum::GOPAY,
                'gopay_payment_id' => (string) $response->json['id'],
                'gopay_order_number' => $response->json['order_number'] ?? null,
            ]);

            $this->redirect($response->json['gw_url']);

            return;
        }

        throw new \RuntimeException('GoPay payment creation failed.');
    }

    protected function handleBankTransfer(): void
    {
        $this->ensureBankTransferDetails();
        $this->generateQrCode();
        $this->isProcessing = false;
    }

    protected function ensureBankTransferDetails(): void
    {
        if (! $this->payment->variable_symbol) {
            $paymentService = app(PaymentService::class);
            $this->payment->update([
                'payment_method' => PaymentMethodEnum::BANK_TRANSFER,
                'variable_symbol' => $paymentService->generateVariableSymbol(),
            ]);
            $this->payment->refresh();
        }
    }

    protected function handleCashPayment(): void
    {
        $this->payment->update([
            'payment_method' => PaymentMethodEnum::CASH,
        ]);

        $this->isProcessing = false;
    }

    protected function generateQrCode(): void
    {
        $team = $this->payment->team;

        if (! $team?->bank_account_iban) {
            return;
        }

        $country = $this->payment->currency === 'CZK' ? 'CZ' : 'SK';
        $qrService = app(QrPaymentService::class);
        $this->qrCodeImage = $qrService->generateQrCode($this->payment, $country);
    }

    /**
     * @return list<string>
     */
    public function getEnabledMethodsProperty(): array
    {
        return $this->payment->team?->getEnabledPaymentMethodKeys() ?? [];
    }

    public function getPayableTypeLabelProperty(): string
    {
        return match ($this->payment->payable_type) {
            'membership' => __('event_detail.dr_membership', [], 'sk') !== 'event_detail.dr_membership' ? __('event_detail.dr_membership') : 'Členstvo',
            'training_registration' => 'Tréning',
            'competition_registration', 'event_registration' => 'Podujatie',
            default => 'Platba',
        };
    }

    public function getTitleProperty(): string
    {
        return match ($this->payment->payable_type) {
            'membership' => 'Uhradiť členské',
            'training_registration' => 'Uhradiť tréning',
            'competition_registration', 'event_registration' => 'Uhradiť podujatie',
            default => 'Uhradiť platbu',
        };
    }

    public function getPayableNameProperty(): ?string
    {
        $payable = $this->payment->payable;

        if (! $payable) {
            return null;
        }

        if ($payable instanceof EventRegistration) {
            return $payable->event?->getTranslation('title', app()->getLocale());
        }

        if ($payable instanceof TrainingRegistration) {
            return $payable->training?->getTranslation('title', app()->getLocale());
        }

        return null;
    }

    public function getFormattedAmountProperty(): string
    {
        $symbol = $this->payment->currency === 'CZK' ? 'Kc' : "\u{20AC}";

        return number_format((float) $this->payment->amount, 2, ',', ' ').' '.$symbol;
    }

    public function render(): View
    {
        return view('livewire.payment-page');
    }
}
