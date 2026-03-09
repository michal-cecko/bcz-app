<?php

namespace App\Mason\Bricks;

use App\Mason\Support\BrickRichEditor;
use App\Mason\Support\TranslatableBrickFields;
use Awcodes\Mason\Brick;
use Filament\Actions\Action;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;

class TimelineBrick extends Brick
{
    public static function getId(): string
    {
        return 'timeline';
    }

    public static function getLabel(): string
    {
        return __('bricks.names.timeline');
    }

    public static function getIcon(): string|Heroicon|Htmlable|null
    {
        return Heroicon::OutlinedClock;
    }

    public static function toHtml(array $config, ?array $data = null): ?string
    {
        return view('mason.bricks.timeline.index', $config)->render();
    }

    public static function configureBrickAction(Action $action): Action
    {
        return $action
            ->slideOver()
            ->schema([
                Repeater::make('items')
                    ->label(__('bricks.timeline.items'))
                    ->schema([
                        TextInput::make('year')
                            ->label(__('bricks.fields.year'))
                            ->required(),
                        TranslatableBrickFields::group(fn (string $locale) => [
                            TextInput::make("title.{$locale}")
                                ->label(__('bricks.fields.title'))
                                ->required(),
                            BrickRichEditor::make("description.{$locale}")
                                ->label(__('bricks.fields.description')),
                        ]),
                    ])
                    ->reorderable()
                    ->reorderableWithButtons()
                    ->cloneable()
                    ->collapsible(),
            ]);
    }
}
