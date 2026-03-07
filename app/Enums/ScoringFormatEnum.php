<?php

namespace App\Enums;

use App\Enums\Concerns\EnumHelper;
use Filament\Support\Contracts\HasLabel;

enum ScoringFormatEnum: string implements HasLabel
{
    use EnumHelper;

    case POINTS = 'points';
    case COACH_DECISION = 'coach_decision';

    public function getLabel(): string
    {
        return $this->translation();
    }
}
