<?php

namespace App\Contracts;

interface Payable
{
    public function getPaymentDescription(): string;

    public function getTotalPriceAmount(): float;

    public function getPriceCurrency(): string;

    public function getQrPaymentNote(): ?string;
}
