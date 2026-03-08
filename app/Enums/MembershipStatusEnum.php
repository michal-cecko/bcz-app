<?php

namespace App\Enums;

use App\Enums\Concerns\EnumHelper;
use BackedEnum;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;
use Filament\Support\Icons\Heroicon;

enum MembershipStatusEnum: string implements HasColor, HasIcon, HasLabel
{
    use EnumHelper;

    case ACTIVE = 'active';
    case EXPIRED = 'expired';
    case CANCELLED = 'cancelled';
    case PENDING = 'pending';

    public function getColor(): string
    {
        return match ($this) {
            self::ACTIVE => 'success',
            self::EXPIRED => 'danger',
            self::CANCELLED => 'gray',
            self::PENDING => 'warning',
        };
    }

    public function getIcon(): string|BackedEnum
    {
        return match ($this) {
            self::ACTIVE => Heroicon::CheckCircle,
            self::EXPIRED => Heroicon::XCircle,
            self::CANCELLED => Heroicon::NoSymbol,
            self::PENDING => Heroicon::Clock,
        };
    }
}
