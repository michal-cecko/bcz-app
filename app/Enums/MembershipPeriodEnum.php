<?php

namespace App\Enums;

use App\Enums\Concerns\EnumHelper;
use Filament\Support\Contracts\HasLabel;

enum MembershipPeriodEnum: string implements HasLabel
{
    use EnumHelper;

    case MONTHLY = 'monthly';
    case YEARLY = 'yearly';

    public function getLabel(): string
    {
        return $this->translation();
    }
}
