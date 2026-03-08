<?php

namespace App\Enums;

use App\Enums\Concerns\EnumHelper;
use BackedEnum;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;
use Filament\Support\Icons\Heroicon;

enum PayoutStatusEnum: string implements HasColor, HasIcon, HasLabel
{
    use EnumHelper;

    case PENDING = 'pending';
    case PROCESSING = 'processing';
    case COMPLETED = 'completed';
    case FAILED = 'failed';

    public function getColor(): string
    {
        return match ($this) {
            self::PENDING => 'warning',
            self::PROCESSING => 'info',
            self::COMPLETED => 'success',
            self::FAILED => 'danger',
        };
    }

    public function getIcon(): string|BackedEnum
    {
        return match ($this) {
            self::PENDING => Heroicon::Clock,
            self::PROCESSING => Heroicon::ArrowPath,
            self::COMPLETED => Heroicon::CheckCircle,
            self::FAILED => Heroicon::XCircle,
        };
    }
}
