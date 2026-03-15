<?php

namespace App\Enums;

use App\Enums\Concerns\EnumHelper;
use Filament\Support\Contracts\HasLabel;

enum EventPricingTypeEnum: string implements HasLabel
{
    use EnumHelper;

    case Free = 'free';
    case Paid = 'paid';
}
