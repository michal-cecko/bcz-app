<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum MenuLocationEnum: string implements HasLabel
{
    case Header = 'header';
    case FooterDiscover = 'footer_discover';
    case FooterPrograms = 'footer_programs';

    public function getLabel(): string
    {
        return __('enums.'.self::class.'.'.$this->value);
    }
}
