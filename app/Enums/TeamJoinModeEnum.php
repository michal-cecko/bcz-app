<?php

namespace App\Enums;

use App\Enums\Concerns\EnumHelper;
use Filament\Support\Contracts\HasLabel;

enum TeamJoinModeEnum: string implements HasLabel
{
    use EnumHelper;

    case APPROVAL = 'approval';
    case OPEN = 'open';
}
