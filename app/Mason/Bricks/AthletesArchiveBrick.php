<?php

namespace App\Mason\Bricks;

use Awcodes\Mason\Brick;
use Filament\Actions\Action;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;

class AthletesArchiveBrick extends Brick
{
    public static function getId(): string
    {
        return 'athletes-archive';
    }

    public static function getLabel(): string
    {
        return 'Archív atlétov';
    }

    public static function getIcon(): string|Heroicon|Htmlable|null
    {
        return Heroicon::OutlinedTrophy;
    }

    public static function toHtml(array $config, ?array $data = null): ?string
    {
        return \Illuminate\Support\Facades\Blade::render(
            '<section class="bg-bcz-dark py-20"><div class="max-w-[1440px] mx-auto px-5 md:px-10 lg:px-20"><livewire:athletes-archive /></div></section>'
        );
    }

    public static function configureBrickAction(Action $action): Action
    {
        return $action;
    }
}
