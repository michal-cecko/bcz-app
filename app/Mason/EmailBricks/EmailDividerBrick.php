<?php

namespace App\Mason\EmailBricks;

use Awcodes\Mason\Brick;
use Filament\Actions\Action;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;

class EmailDividerBrick extends Brick
{
    public static function getId(): string
    {
        return 'email-divider';
    }

    public static function getLabel(): string
    {
        return 'Oddeľovač';
    }

    public static function getIcon(): string|Heroicon|Htmlable|null
    {
        return Heroicon::OutlinedMinus;
    }

    public static function toHtml(array $config, ?array $data = null): ?string
    {
        return view('mason.email-bricks.email-divider.index', $config)->render();
    }

    public static function configureBrickAction(Action $action): Action
    {
        return $action->schema([]);
    }
}
