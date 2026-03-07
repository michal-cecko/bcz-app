<?php

namespace App\Enums;

use App\Enums\Concerns\EnumHelper;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum InquiryReasonEnum: string implements HasColor, HasLabel
{
    use EnumHelper;

    case TRAINING = 'training';
    case EXHIBITION = 'exhibition';
    case LECTURE = 'lecture';
    case WORKSHOP = 'workshop';
    case COMPETITION = 'competition';
    case OTHER = 'other';

    public function getLabel(): string
    {
        return $this->translation();
    }

    public function getColor(): string
    {
        return match ($this) {
            self::TRAINING => 'info',
            self::COMPETITION => 'warning',
            self::EXHIBITION => 'success',
            self::LECTURE => 'primary',
            self::WORKSHOP => 'danger',
            self::OTHER => 'gray',
        };
    }
}
