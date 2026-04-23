<?php

namespace App\Enums;

use App\Enums\Concerns\EnumHelper;
use BackedEnum;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;
use Filament\Support\Icons\Heroicon;

enum PaymentStatusEnum: string implements HasColor, HasIcon, HasLabel
{
    use EnumHelper;

    case PENDING = 'pending';
    case COMPLETED = 'completed';
    case FAILED = 'failed';
    case REFUNDED = 'refunded';
    case CANCELLED = 'cancelled';

    public function getColor(): string
    {
        return match ($this) {
            self::PENDING => 'warning',
            self::COMPLETED => 'success',
            self::FAILED => 'danger',
            self::REFUNDED => 'gray',
            self::CANCELLED => 'gray',
        };
    }

    public function getIcon(): string|BackedEnum
    {
        return match ($this) {
            self::PENDING => Heroicon::Clock,
            self::COMPLETED => Heroicon::CheckCircle,
            self::FAILED => Heroicon::XCircle,
            self::REFUNDED => Heroicon::ArrowUturnLeft,
            self::CANCELLED => Heroicon::NoSymbol,
        };
    }
}
