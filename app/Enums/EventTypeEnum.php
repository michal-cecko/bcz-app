<?php

namespace App\Enums;

use App\Enums\Concerns\EnumHelper;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum EventTypeEnum: string implements HasColor, HasLabel
{
    use EnumHelper;

    case Report = 'report';
    case Organized = 'organized';
    case Competition = 'competition';

    public function getColor(): string
    {
        return match ($this) {
            self::Report => 'gray',
            self::Organized => 'success',
            self::Competition => 'warning',
        };
    }
}
