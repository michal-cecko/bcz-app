<?php

namespace App\Enums;

use App\Enums\Concerns\EnumHelper;
use BackedEnum;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;
use Filament\Support\Icons\Heroicon;

enum InquiryStatusEnum: string implements HasColor, HasIcon, HasLabel
{
    use EnumHelper;

    case NEW = 'new';
    case IN_PROGRESS = 'in_progress';
    case RESOLVED = 'resolved';

    public function getColor(): string
    {
        return match ($this) {
            self::NEW => 'danger',
            self::IN_PROGRESS => 'warning',
            self::RESOLVED => 'success',
        };
    }

    public function getIcon(): string|BackedEnum
    {
        return match ($this) {
            self::NEW => Heroicon::ExclamationCircle,
            self::IN_PROGRESS => Heroicon::ArrowPath,
            self::RESOLVED => Heroicon::CheckCircle,
        };
    }
}
