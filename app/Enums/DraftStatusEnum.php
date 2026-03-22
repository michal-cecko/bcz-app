<?php

namespace App\Enums;

use App\Enums\Concerns\EnumHelper;
use BackedEnum;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;
use Filament\Support\Icons\Heroicon;

enum DraftStatusEnum: string implements HasColor, HasIcon, HasLabel
{
    use EnumHelper;

    case Pending = 'pending';
    case Rejected = 'rejected';

    public function getColor(): string
    {
        return match ($this) {
            self::Pending => 'warning',
            self::Rejected => 'danger',
        };
    }

    public function getIcon(): string|BackedEnum
    {
        return match ($this) {
            self::Pending => Heroicon::Clock,
            self::Rejected => Heroicon::XCircle,
        };
    }
}
