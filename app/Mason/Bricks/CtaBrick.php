<?php

namespace App\Mason\Bricks;

use Awcodes\Mason\Brick;
use Filament\Actions\Action;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\TextInput;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;

class CtaBrick extends Brick
{
    public static function getId(): string
    {
        return 'cta';
    }

    public static function getLabel(): string
    {
        return 'Call to Action';
    }

    public static function getIcon(): string|Heroicon|Htmlable|null
    {
        return Heroicon::OutlinedMegaphone;
    }

    public static function toHtml(array $config, ?array $data = null): ?string
    {
        return view('mason.bricks.cta.index', $config)->render();
    }

    public static function configureBrickAction(Action $action): Action
    {
        return $action
            ->slideOver()
            ->schema([
                TextInput::make('title')
                    ->required(),
                TextInput::make('description'),
                TextInput::make('button_text'),
                TextInput::make('button_url')
                    ->url(),
                ColorPicker::make('background_color'),
            ]);
    }
}
