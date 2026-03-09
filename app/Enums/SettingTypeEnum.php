<?php

namespace App\Enums;

use App\Enums\Concerns\EnumHelper;
use Filament\Support\Contracts\HasLabel;

enum SettingTypeEnum: string implements HasLabel
{
    use EnumHelper;

    case TEXT = 'text';
    case NUMBER = 'number';
    case BOOLEAN = 'boolean';
    case SELECT = 'select';
    case MULTI_SELECT = 'multi_select';
    case TEAM_SELECT = 'team_select';
    case DATE = 'date';
}
