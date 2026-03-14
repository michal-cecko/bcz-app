<?php

namespace App\Enums;

use App\Enums\Concerns\EnumHelper;
use BackedEnum;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;
use Filament\Support\Icons\Heroicon;

enum JoinRequestStatusEnum: string implements HasColor, HasIcon, HasLabel
{
    use EnumHelper;

    case Pending = 'pending';
    case Approved = 'approved';
    case Rejected = 'rejected';

    public function getColor(): string
    {
        return match ($this) {
            self::Pending => 'warning',
            self::Approved => 'success',
            self::Rejected => 'danger',
        };
    }

    public function getIcon(): string|BackedEnum
    {
        return match ($this) {
            self::Pending => Heroicon::Clock,
            self::Approved => Heroicon::CheckCircle,
            self::Rejected => Heroicon::XCircle,
        };
    }
}
