<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum TimetableEntryTypeEnum: string implements HasLabel
{
    case COMPETITION_ROUND = 'competition_round';

    public function getLabel(): string
    {
        return match ($this) {
            self::COMPETITION_ROUND => 'Súťažné kolo',
        };
    }
}
