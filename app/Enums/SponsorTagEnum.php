<?php

namespace App\Enums;

use App\Enums\Concerns\EnumHelper;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum SponsorTagEnum: string implements HasColor, HasLabel
{
    use EnumHelper;

    case MAIN_SPONSOR = 'main_sponsor';
    case MEDIAL_SPONSOR = 'medial_sponsor';
    case PARTNER = 'partner';
    case SUPPORTER = 'supporter';

    public function getLabel(): string
    {
        return $this->translation();
    }

    public function getColor(): string
    {
        return match ($this) {
            self::MAIN_SPONSOR => 'success',
            self::MEDIAL_SPONSOR => 'info',
            self::PARTNER => 'warning',
            self::SUPPORTER => 'gray',
        };
    }
}
