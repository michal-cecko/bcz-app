<?php

namespace App\Enums;

use App\Enums\Concerns\EnumHelper;
use Filament\Support\Contracts\HasLabel;

enum PayableTypeEnum: string implements HasLabel
{
    use EnumHelper;

    case MEMBERSHIP = 'membership';
    case TRAINING_REGISTRATION = 'training_registration';
    case COMPETITION_REGISTRATION = 'competition_registration';
}
