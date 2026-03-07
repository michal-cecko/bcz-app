<?php

namespace App\Mason\Bricks;

use Awcodes\Mason\Brick;
use Filament\Actions\Action;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;
use RalphJSmit\Filament\MediaLibrary\Filament\Forms\Components\MediaPicker;

class GalleryBrick extends Brick
{
    public static function getId(): string
    {
        return 'gallery';
    }

    public static function getIcon(): string|Heroicon|Htmlable|null
    {
        return Heroicon::OutlinedRectangleGroup;
    }

    public static function toHtml(array $config, ?array $data = null): ?string
    {
        return view('mason.bricks.gallery.index', $config)->render();
    }

    public static function configureBrickAction(Action $action): Action
    {
        return $action
            ->slideOver()
            ->schema([
                MediaPicker::make('images')
                    ->multiple()
                    ->reorderable(),
            ]);
    }
}
