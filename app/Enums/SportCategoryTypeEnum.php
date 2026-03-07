<?php

namespace App\Enums;

use App\Enums\Concerns\EnumHelper;

enum SportCategoryTypeEnum: string
{
    use EnumHelper;

    case CALISTHENICS = 'calisthenics';
    case PARKOUR = 'parkour';
}
