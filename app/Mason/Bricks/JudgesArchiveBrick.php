<?php

namespace App\Mason\Bricks;

use Awcodes\Mason\Brick;
use Filament\Actions\Action;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;

class JudgesArchiveBrick extends Brick
{
    public static function getId(): string
    {
        return 'judges-archive';
    }

    public static function getLabel(): string
    {
        return 'Archív rozhodcov';
    }

    public static function getIcon(): string|Heroicon|Htmlable|null
    {
        return Heroicon::OutlinedScale;
    }

    public static function toHtml(array $config, ?array $data = null): ?string
    {
        return \Illuminate\Support\Facades\Blade::render(
            '<section class="bg-bcz-dark py-20"><div class="max-w-[1440px] mx-auto px-5 md:px-10 lg:px-20"><livewire:judges-archive /></div></section>'
        );
    }

    public static function configureBrickAction(Action $action): Action
    {
        return $action;
    }
}
