<?php

namespace App\Enums;

use App\Enums\Concerns\EnumHelper;
use Filament\Support\Contracts\HasLabel;

enum BillingPeriodEnum: string implements HasLabel
{
    use EnumHelper;

    case MONTHLY = 'monthly';
    case YEARLY = 'yearly';
}
