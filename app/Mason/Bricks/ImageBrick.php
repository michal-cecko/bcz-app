<?php

namespace App\Mason\Bricks;

use App\Mason\Support\TranslatableBrickFields;
use Awcodes\Mason\Brick;
use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;
use RalphJSmit\Filament\MediaLibrary\Filament\Forms\Components\MediaPicker;

class ImageBrick extends Brick
{
    public static function getId(): string
    {
        return 'image';
    }

    public static function getLabel(): string
    {
        return __('bricks.names.image');
    }

    public static function getIcon(): string|Heroicon|Htmlable|null
    {
        return Heroicon::OutlinedPhoto;
    }

    public static function toHtml(array $config, ?array $data = null): ?string
    {
        return view('mason.bricks.image.index', $config)->render();
    }

    public static function configureBrickAction(Action $action): Action
    {
        return $action
            ->slideOver()
            ->schema([
                MediaPicker::make('image')
                    ->label(__('bricks.fields.image'))
                    ->required(),
                TranslatableBrickFields::group(fn (string $locale) => [
                    TextInput::make("alt.{$locale}")
                        ->label(__('bricks.image.alt')),
                    TextInput::make("caption.{$locale}")
                        ->label(__('bricks.image.caption')),
                ]),
            ]);
    }
}
