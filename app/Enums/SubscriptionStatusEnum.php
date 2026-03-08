<?php

namespace App\Enums;

use App\Enums\Concerns\EnumHelper;
use BackedEnum;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;
use Filament\Support\Icons\Heroicon;

enum SubscriptionStatusEnum: string implements HasColor, HasIcon, HasLabel
{
    use EnumHelper;

    case ACTIVE = 'active';
    case TRIALING = 'trialing';
    case PAST_DUE = 'past_due';
    case CANCELLED = 'cancelled';
    case EXPIRED = 'expired';

    public function getColor(): string
    {
        return match ($this) {
            self::ACTIVE => 'success',
            self::TRIALING => 'info',
            self::PAST_DUE => 'warning',
            self::CANCELLED => 'gray',
            self::EXPIRED => 'danger',
        };
    }

    public function getIcon(): string|BackedEnum
    {
        return match ($this) {
            self::ACTIVE => Heroicon::CheckCircle,
            self::TRIALING => Heroicon::Clock,
            self::PAST_DUE => Heroicon::ExclamationTriangle,
            self::CANCELLED => Heroicon::NoSymbol,
            self::EXPIRED => Heroicon::XCircle,
        };
    }
}
