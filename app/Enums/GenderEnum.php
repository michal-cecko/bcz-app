<?php

namespace App\Enums;

use App\Enums\Concerns\EnumHelper;
use Filament\Support\Contracts\HasLabel;

enum GenderEnum: string implements HasLabel
{
    use EnumHelper;

    case MALE = 'male';
    case FEMALE = 'female';

    public function getLabel(): string
    {
        return $this->translation();
    }
}
