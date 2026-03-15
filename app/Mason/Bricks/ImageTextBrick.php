<?php

namespace App\Mason\Bricks;

use App\Mason\Support\BrickRichEditor;
use App\Mason\Support\TranslatableBrickFields;
use Awcodes\Mason\Brick;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ToggleButtons;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;

class ImageTextBrick extends Brick
{
    public static function getId(): string
    {
        return 'image-text';
    }

    public static function getLabel(): string
    {
        return __('bricks.names.image-text');
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
                FileUpload::make('image')
                    ->image()
                    ->disk('public')
                    ->directory('bricks')
                    ->visibility('public')
                    ->label(__('bricks.fields.image'))
                    ->required(),
                ToggleButtons::make('image_position')
                    ->label(__('bricks.image_text.image_position'))
                    ->options([
                        'left' => __('bricks.image_text.image_left'),
                        'right' => __('bricks.image_text.image_right'),
                    ])
                    ->default('left')
                    ->grouped(),
                TranslatableBrickFields::group(fn (string $locale) => [
                    TextInput::make("alt.{$locale}")
                        ->label(__('bricks.image_text.alt')),
                    BrickRichEditor::make("text.{$locale}")
                        ->label(__('bricks.fields.text'))
                        ->required(),
                ]),
            ]);
    }
}
