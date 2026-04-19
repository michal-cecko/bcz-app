<?php

namespace App\Enums;

use App\Enums\Concerns\EnumHelper;
use Filament\Support\Contracts\HasLabel;

enum PairingStrategyEnum: string implements HasLabel
{
    use EnumHelper;

    case RANDOM = 'random';
    case SEEDED = 'seeded';
}
