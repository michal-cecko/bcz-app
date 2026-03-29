<?php

namespace App\Mason\Bricks;

use Awcodes\Mason\Brick;
use Filament\Actions\Action;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\Blade;

class TrainingsArchiveBrick extends Brick
{
    public static function getId(): string
    {
        return 'trainings-archive';
    }

    public static function getLabel(): string
    {
        return __('bricks.names.trainings-archive');
    }

    public static function getIcon(): string|Heroicon|Htmlable|null
    {
        return Heroicon::OutlinedCalendarDays;
    }

    public static function toHtml(array $config, ?array $data = null): ?string
    {
        return Blade::render(
            '<section class="bg-bcz-dark py-20"><div class="max-w-[1440px] mx-auto px-5 md:px-10 lg:px-20"><livewire:trainings-archive /></div></section>'
        );
    }

    public static function configureBrickAction(Action $action): Action
    {
        return $action;
    }
}
