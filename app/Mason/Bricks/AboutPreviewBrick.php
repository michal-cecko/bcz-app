<?php

namespace App\Mason\Bricks;

use App\Mason\Support\BrickRichEditor;
use App\Mason\Support\LinkPickerField;
use App\Mason\Support\TranslatableBrickFields;
use Awcodes\Mason\Brick;
use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;
use RalphJSmit\Filament\MediaLibrary\Filament\Forms\Components\MediaPicker;

class AboutPreviewBrick extends Brick
{
    public static function getId(): string
    {
        return 'about-preview';
    }

    public static function getLabel(): string
    {
        return __('bricks.names.about-preview');
    }

    public static function getIcon(): string|Heroicon|Htmlable|null
    {
        return Heroicon::OutlinedUserGroup;
    }

    public static function toHtml(array $config, ?array $data = null): ?string
    {
        return view('mason.bricks.about-preview.index', $config)->render();
    }

    public static function configureBrickAction(Action $action): Action
    {
        return $action
            ->slideOver()
            ->schema([
                MediaPicker::make('image_main')
                    ->label(__('bricks.about_preview.image_main')),
                MediaPicker::make('image_left')
                    ->label(__('bricks.about_preview.image_left')),
                MediaPicker::make('image_right')
                    ->label(__('bricks.about_preview.image_right')),
                TranslatableBrickFields::group(fn (string $locale) => [
                    TextInput::make("label.{$locale}")
                        ->label(__('bricks.fields.label')),
                    TextInput::make("title.{$locale}")
                        ->label(__('bricks.fields.title'))
                        ->required(),
                    BrickRichEditor::make("description.{$locale}")
                        ->label(__('bricks.fields.description')),
                    LinkPickerField::make('cta_', $locale, null, 'cta_text', __('bricks.about_preview.cta_text')),
                    TextInput::make("image_caption.{$locale}")
                        ->label(__('bricks.about_preview.image_caption')),
                ]),
            ]);
    }
}
