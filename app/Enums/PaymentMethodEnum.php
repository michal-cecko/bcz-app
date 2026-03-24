<?php

namespace App\Enums;

use App\Enums\Concerns\EnumHelper;
use BackedEnum;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;
use Filament\Support\Icons\Heroicon;

enum PaymentMethodEnum: string implements HasColor, HasIcon, HasLabel
{
    use EnumHelper;

    case BANK_TRANSFER = 'bank_transfer';
    case CASH = 'cash';
    case GOPAY = 'gopay';

    public function getColor(): string
    {
        return match ($this) {
            self::BANK_TRANSFER => 'info',
            self::CASH => 'warning',
            self::GOPAY => 'primary',
        };
    }

    public function getIcon(): string|BackedEnum
    {
        return match ($this) {
            self::BANK_TRANSFER => Heroicon::BuildingLibrary,
            self::CASH => Heroicon::CurrencyEuro,
            self::GOPAY => Heroicon::CreditCard,
        };
    }
}
