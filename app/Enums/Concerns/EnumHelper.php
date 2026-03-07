<?php

namespace App\Enums\Concerns;

trait EnumHelper
{
    public function translation(): string
    {
        $key = 'enums.'.static::class.'.'.$this->value;

        return __($key);
    }

    /**
     * @return array<string, string>
     */
    public static function translations(): array
    {
        return collect(static::cases())
            ->mapWithKeys(fn (self $case) => [$case->value => $case->translation()])
            ->toArray();
    }
}
