<?php

namespace App\Mason\Bricks;

use Awcodes\Mason\Brick;
use Filament\Actions\Action;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;

class EventsArchiveBrick extends Brick
{
    public static function getId(): string
    {
        return 'events-archive';
    }

    public static function getLabel(): string
    {
        return __('bricks.names.events-archive');
    }

    public static function getIcon(): string|Heroicon|Htmlable|null
    {
        return Heroicon::OutlinedStar;
    }

    public static function toHtml(array $config, ?array $data = null): ?string
    {
        return \Illuminate\Support\Facades\Blade::render(
            '<section class="bg-bcz-dark py-20"><div class="max-w-[1440px] mx-auto px-5 md:px-10 lg:px-20"><livewire:events-archive /></div></section>'
        );
    }

    public static function configureBrickAction(Action $action): Action
    {
        return $action;
    }
}
