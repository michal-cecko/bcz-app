<?php

namespace App\Enums;

use App\Enums\Concerns\EnumHelper;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum CoachRoleEnum: string implements HasColor, HasLabel
{
    use EnumHelper;

    case MAIN = 'main';
    case SECONDARY = 'secondary';

    public function getLabel(): string
    {
        return $this->translation();
    }

    public function getColor(): string
    {
        return match ($this) {
            self::MAIN => 'primary',
            self::SECONDARY => 'gray',
        };
    }
}
