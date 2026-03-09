<?php

namespace App\Contracts;

use Illuminate\Support\Collection;

interface Linkable
{
    public function getLinkUrl(): string;

    public function getLinkLabel(): string;

    /**
     * @return Collection<string, string>
     */
    public static function linkableOptions(): Collection;
}
