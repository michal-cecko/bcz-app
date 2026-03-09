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

class NumberedStepsBrick extends Brick
{
    public static function getId(): string
    {
        return 'numbered-steps';
    }

    public static function getLabel(): string
    {
        return __('bricks.names.numbered-steps');
    }

    public static function getIcon(): string|Heroicon|Htmlable|null
    {
        return Heroicon::OutlinedListBullet;
    }

    public static function toHtml(array $config, ?array $data = null): ?string
    {
        return view('mason.bricks.numbered-steps.index', $config)->render();
    }

    public static function configureBrickAction(Action $action): Action
    {
        return $action
            ->slideOver()
            ->schema([
                Repeater::make('steps')
                    ->label(__('bricks.numbered_steps.steps'))
                    ->schema([
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
