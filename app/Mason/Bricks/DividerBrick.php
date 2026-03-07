<?php

namespace App\Mason\Bricks;

use Awcodes\Mason\Brick;
use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;

class DividerBrick extends Brick
{
    public static function getId(): string
    {
        return 'divider';
    }

    public static function getIcon(): string|Heroicon|Htmlable|null
    {
        return Heroicon::OutlinedMinus;
    }

    public static function toHtml(array $config, ?array $data = null): ?string
    {
        return view('mason.bricks.divider.index', $config)->render();
    }

    public static function configureBrickAction(Action $action): Action
    {
        return $action
            ->schema([
                TextInput::make('label')
                    ->placeholder('Optional label'),
            ]);
    }
}
