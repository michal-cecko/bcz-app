<?php

namespace App\Mason\Bricks;

use App\Mason\Support\BrickRichEditor;
use App\Mason\Support\TranslatableBrickFields;
use Awcodes\Mason\Brick;
use Filament\Actions\Action;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;

class SkillCardsBrick extends Brick
{
    public static function getId(): string
    {
        return 'skill-cards';
    }

    public static function getLabel(): string
    {
        return __('bricks.names.skill-cards');
    }

    public static function getIcon(): string|Heroicon|Htmlable|null
    {
        return Heroicon::OutlinedAcademicCap;
    }

    public static function toHtml(array $config, ?array $data = null): ?string
    {
        return view('mason.bricks.skill-cards.index', $config)->render();
    }

    public static function configureBrickAction(Action $action): Action
    {
        return $action
            ->slideOver()
            ->schema([
                Repeater::make('levels')
                    ->label(__('bricks.skill_cards.levels'))
                    ->schema([
                        ColorPicker::make('color')
                            ->label(__('bricks.fields.color')),
                        TranslatableBrickFields::group(fn (string $locale) => [
                            TextInput::make("name.{$locale}")
                                ->label(__('bricks.fields.name'))
                                ->required(),
                        ]),
                        Repeater::make('cards')
                            ->label(__('bricks.skill_cards.cards'))
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
                    ])
                    ->collapsible()
                    ->reorderable()
                    ->reorderableWithButtons()
                    ->cloneable(),
            ]);
    }
}
