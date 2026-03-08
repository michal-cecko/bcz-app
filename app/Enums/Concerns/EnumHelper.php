<?php

namespace App\Enums\Concerns;

trait EnumHelper
{
    public function translation(): string
    {
        $key = 'enums.'.static::class.'.'.$this->value;

        return __($key);
    }

    public function getLabel(): string
    {
        return $this->translation();
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
