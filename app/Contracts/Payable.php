<?php

namespace App\Contracts;

interface Payable
{
    public function getPaymentDescription(): string;

    public function getTotalPriceAmount(): float;

    public function getPriceCurrency(): string;

    public function getQrPaymentNote(): ?string;

    /**
     * IBAN to credit for this payment, with team-default fallback applied.
     */
    public function getPayoutIban(): ?string;

    /**
     * Recipient name to credit for this payment, with team-default fallback applied.
     */
    public function getPayoutRecipientName(): ?string;
}
