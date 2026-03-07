<?php

namespace App\Enums;

use App\Enums\Concerns\EnumHelper;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum GoalStatusEnum: string implements HasColor, HasLabel
{
    use EnumHelper;

    case PLANNED = 'planned';
    case IN_PROGRESS = 'in_progress';
    case ACTIVE = 'active';
    case COMPLETED = 'completed';

    public function getLabel(): string
    {
        return $this->translation();
    }

    public function getColor(): string
    {
        return match ($this) {
            self::PLANNED => 'gray',
            self::IN_PROGRESS => 'warning',
            self::ACTIVE => 'info',
            self::COMPLETED => 'success',
        };
    }
}
