<?php

namespace App\Enums;

use App\Enums\Concerns\EnumHelper;
use BackedEnum;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;
use Filament\Support\Icons\Heroicon;

enum TimetableEntryStatusEnum: string implements HasColor, HasIcon, HasLabel
{
    use EnumHelper;

    case PENDING = 'pending';
    case IN_PROGRESS = 'in_progress';
    case FINISHED = 'finished';

    public function getColor(): string
    {
        return match ($this) {
            self::PENDING => 'gray',
            self::IN_PROGRESS => 'warning',
            self::FINISHED => 'success',
        };
    }

    public function getIcon(): string|BackedEnum
    {
        return match ($this) {
            self::PENDING => Heroicon::Clock,
            self::IN_PROGRESS => Heroicon::PlayCircle,
            self::FINISHED => Heroicon::CheckCircle,
        };
    }
}
