<?php

namespace App\Mason\Bricks;

use App\Mason\Support\BrickRichEditor;
use App\Mason\Support\LinkPickerField;
use App\Mason\Support\TranslatableBrickFields;
use Awcodes\Mason\Brick;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;

class SportCategoriesBrick extends Brick
{
    public static function getId(): string
    {
        return 'sport-categories';
    }

    public static function getLabel(): string
    {
        return __('bricks.names.sport-categories');
    }

    public static function getIcon(): string|Heroicon|Htmlable|null
    {
        return Heroicon::OutlinedRectangleGroup;
    }

    public static function toHtml(array $config, ?array $data = null): ?string
    {
        return view('mason.bricks.sport-categories.index', $config)->render();
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
                        ->label(__('bricks.fields.title'))
                        ->required(),
                    TextInput::make("subtitle.{$locale}")
                        ->label(__('bricks.fields.subtitle')),
                ]),
                Repeater::make('categories')
                    ->label(__('bricks.sport_categories.categories'))
                    ->schema([
                        FileUpload::make('image')
                            ->image()
                            ->disk('public')
                            ->directory('bricks')
                            ->visibility('public')
                            ->label(__('bricks.fields.image')),
                        TranslatableBrickFields::group(fn (string $locale) => [
                            TextInput::make("title.{$locale}")
                                ->label(__('bricks.fields.title'))
                                ->required(),
                            BrickRichEditor::make("description.{$locale}")
                                ->label(__('bricks.fields.description')),
                            LinkPickerField::make('link_', $locale, null, 'link_text', __('bricks.sport_categories.link_text')),
                        ]),
                    ])
                    ->reorderable()
                    ->reorderableWithButtons()
                    ->cloneable()
                    ->collapsible(),
            ]);
    }
}
