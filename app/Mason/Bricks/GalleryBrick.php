<?php

namespace App\Mason\Bricks;

use App\Mason\Support\TranslatableBrickFields;
use Awcodes\Mason\Brick;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;

class GalleryBrick extends Brick
{
    public static function getId(): string
    {
        return 'gallery';
    }

    public static function getLabel(): string
    {
        return __('bricks.names.gallery');
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
                TranslatableBrickFields::group(fn (string $locale) => [
                    TextInput::make("label.{$locale}")
                        ->label(__('bricks.fields.label')),
                    TextInput::make("title.{$locale}")
                        ->label(__('bricks.fields.title')),
                ]),
                FileUpload::make('images')
                    ->label(__('bricks.gallery.images'))
                    ->image()
                    ->multiple()
                    ->reorderable()
                    ->panelLayout('grid')
                    ->disk('public')
                    ->directory('bricks')
                    ->visibility('public'),
            ]);
    }
}
