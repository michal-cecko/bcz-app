<?php

namespace App\Enums;

use App\Enums\Concerns\EnumHelper;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum ComplexityLevelEnum: string implements HasColor, HasLabel
{
    use EnumHelper;

    case BASIC = 'basic';
    case INTERMEDIATE = 'intermediate';
    case ADVANCED = 'advanced';
    case ELITE = 'elite';

    public function getColor(): string
    {
        return match ($this) {
            self::BASIC => 'gray',
            self::INTERMEDIATE => 'info',
            self::ADVANCED => 'warning',
            self::ELITE => 'danger',
        };
    }
}
