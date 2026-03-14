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

class VerticalTimelineBrick extends Brick
{
    public static function getId(): string
    {
        return 'vertical-timeline';
    }

    public static function getLabel(): string
    {
        return __('bricks.names.vertical-timeline');
    }

    public static function getIcon(): string|Heroicon|Htmlable|null
    {
        return Heroicon::OutlinedClock;
    }

    public static function toHtml(array $config, ?array $data = null): ?string
    {
        return view('mason.bricks.vertical-timeline.index', $config)->render();
    }

    public static function configureBrickAction(Action $action): Action
    {
        return $action
            ->slideOver()
            ->schema([
                TranslatableBrickFields::group(fn (string $locale) => [
                    TextInput::make("label.{$locale}")
                        ->label('Označenie'),
                    TextInput::make("title.{$locale}")
                        ->label('Nadpis'),
                ]),
                Repeater::make('items')
                    ->schema([
                        TextInput::make('year')
                            ->label('Rok')
                            ->required(),
                        TranslatableBrickFields::group(fn (string $locale) => [
                            TextInput::make("title.{$locale}")
                                ->label('Nadpis')
                                ->required(),
                            BrickRichEditor::make("description.{$locale}")
                                ->label('Popis'),
                        ]),
                    ])
                    ->reorderable()
                    ->reorderableWithButtons()
                    ->cloneable()
                    ->collapsible(),
            ]);
    }
}
