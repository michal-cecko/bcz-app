<?php

namespace App\Enums;

use App\Enums\Concerns\EnumHelper;
use Filament\Support\Contracts\HasLabel;

enum ProfileTypeEnum: string implements HasLabel
{
    use EnumHelper;

    case Coach = 'coach';
    case Athlete = 'athlete';
    case Judge = 'judge';
}
