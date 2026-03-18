<?php

namespace App\Enums;

use App\Enums\Concerns\EnumHelper;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum BannerTypeEnum: string implements HasColor, HasLabel
{
    use EnumHelper;

    case Topbar = 'topbar';
    case Floating = 'floating';
    case Popup = 'popup';

    public function getColor(): string
    {
        return match ($this) {
            self::Topbar => 'info',
            self::Floating => 'warning',
            self::Popup => 'danger',
        };
    }
}
