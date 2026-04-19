<?php

namespace App\Contracts;

interface Payable
{
    public function getPaymentDescription(): string;
}
