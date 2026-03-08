<?php

namespace App\Enums;

use App\Enums\Concerns\EnumHelper;
use Filament\Support\Contracts\HasLabel;

enum RoleEnum: string implements HasLabel
{
    use EnumHelper;

    case SUPER_ADMIN = 'SUPERADMIN';
    case OWNER = 'OWNER';
    case ADMIN = 'ADMIN';
    case TEAM_ADMIN = 'TEAMADMIN';
    case COACH = 'COACH';
    case ATHLETE = 'ATHLETE';
    case EDITOR = 'EDITOR';
    case JUDGE = 'JUDGE';
    case CUSTOMER = 'CUSTOMER';
}
