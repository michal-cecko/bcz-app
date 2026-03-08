<?php

namespace App\Enums;

use App\Enums\Concerns\EnumHelper;
use BackedEnum;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;
use Filament\Support\Icons\Heroicon;

enum PlanTierEnum: string implements HasColor, HasIcon, HasLabel
{
    use EnumHelper;

    case FREE = 'free';
    case STARTER = 'starter';
    case PRO = 'pro';
    case ENTERPRISE = 'enterprise';

    public function getColor(): string
    {
        return match ($this) {
            self::FREE => 'gray',
            self::STARTER => 'info',
            self::PRO => 'success',
            self::ENTERPRISE => 'primary',
        };
    }

    public function getIcon(): string|BackedEnum
    {
        return match ($this) {
            self::FREE => Heroicon::Gift,
            self::STARTER => Heroicon::RocketLaunch,
            self::PRO => Heroicon::Star,
            self::ENTERPRISE => Heroicon::BuildingOffice,
        };
    }
}
