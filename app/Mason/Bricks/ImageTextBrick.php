<?php

namespace App\Mason\Bricks;

use Awcodes\Mason\Brick;
use Awcodes\RicherEditor\RicherEditor;
use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ToggleButtons;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;
use RalphJSmit\Filament\MediaLibrary\Filament\Forms\Components\MediaPicker;

class ImageTextBrick extends Brick
{
    public static function getId(): string
    {
        return 'image-text';
    }

    public static function getLabel(): string
    {
        return 'Image + Text';
    }

    public static function getIcon(): string|Heroicon|Htmlable|null
    {
        return Heroicon::OutlinedViewColumns;
    }

    public static function toHtml(array $config, ?array $data = null): ?string
    {
        return view('mason.bricks.image-text.index', $config)->render();
    }

    public static function configureBrickAction(Action $action): Action
    {
        return $action
            ->slideOver()
            ->schema([
                MediaPicker::make('image')
                    ->required(),
                TextInput::make('alt')
                    ->label('Alt text'),
                RicherEditor::make('text')
                    ->required(),
                ToggleButtons::make('image_position')
                    ->options([
                        'left' => 'Image Left',
                        'right' => 'Image Right',
                    ])
                    ->default('left')
                    ->grouped(),
            ]);
    }
}
