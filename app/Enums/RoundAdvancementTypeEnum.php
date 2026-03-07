<?php

namespace App\Enums;

use App\Enums\Concerns\EnumHelper;
use Filament\Support\Contracts\HasLabel;

enum RoundAdvancementTypeEnum: string implements HasLabel
{
    use EnumHelper;

    case TOP_BY_POINTS = 'top_by_points';
    case BATTLE_WINNER = 'battle_winner';

    public function getLabel(): string
    {
        return $this->translation();
    }
}
