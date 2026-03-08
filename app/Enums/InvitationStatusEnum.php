<?php

namespace App\Enums;

use App\Enums\Concerns\EnumHelper;
use BackedEnum;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;
use Filament\Support\Icons\Heroicon;

enum InvitationStatusEnum: string implements HasColor, HasIcon, HasLabel
{
    use EnumHelper;

    case Pending = 'pending';
    case Accepted = 'accepted';
    case Declined = 'declined';
    case Expired = 'expired';

    public function getColor(): string
    {
        return match ($this) {
            self::Pending => 'warning',
            self::Accepted => 'success',
            self::Declined => 'danger',
            self::Expired => 'gray',
        };
    }

    public function getIcon(): string|BackedEnum
    {
        return match ($this) {
            self::Pending => Heroicon::Clock,
            self::Accepted => Heroicon::CheckCircle,
            self::Declined => Heroicon::XCircle,
            self::Expired => Heroicon::ExclamationTriangle,
        };
    }
}
