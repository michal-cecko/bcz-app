<?php

namespace App\Mason\Bricks;

use Awcodes\Mason\Brick;
use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;
use RalphJSmit\Filament\MediaLibrary\Filament\Forms\Components\MediaPicker;

class HeroBrick extends Brick
{
    public static function getId(): string
    {
        return 'hero';
    }

    public static function getIcon(): string|Heroicon|Htmlable|null
    {
        return Heroicon::OutlinedPhoto;
    }

    public static function toHtml(array $config, ?array $data = null): ?string
    {
        return view('mason.bricks.hero.index', $config)->render();
    }

    public static function configureBrickAction(Action $action): Action
    {
        return $action
            ->slideOver()
            ->schema([
                MediaPicker::make('background_image'),
                TextInput::make('title')
                    ->required(),
                TextInput::make('subtitle'),
                TextInput::make('cta_text')
                    ->label('Button text'),
                TextInput::make('cta_url')
                    ->label('Button URL')
                    ->url(),
            ]);
    }
}
