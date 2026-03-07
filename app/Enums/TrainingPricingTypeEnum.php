<?php

namespace App\Enums;

use App\Enums\Concerns\EnumHelper;
use BackedEnum;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;
use Filament\Support\Icons\Heroicon;

enum TrainingPricingTypeEnum: string implements HasColor, HasIcon, HasLabel
{
    use EnumHelper;

    case FREE = 'free';
    case PAID = 'paid';
    case MEMBERSHIP_REQUIRED = 'membership_required';

    public function getLabel(): string
    {
        return $this->translation();
    }

    public function getColor(): string
    {
        return match ($this) {
            self::FREE => 'success',
            self::PAID => 'warning',
            self::MEMBERSHIP_REQUIRED => 'info',
        };
    }

    public function getIcon(): string|BackedEnum
    {
        return match ($this) {
            self::FREE => Heroicon::Gift,
            self::PAID => Heroicon::CurrencyEuro,
            self::MEMBERSHIP_REQUIRED => Heroicon::Identification,
        };
    }
}
