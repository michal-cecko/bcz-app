<?php

namespace App\Mason\Bricks;

use App\Mason\Support\BrickRichEditor;
use App\Mason\Support\TranslatableBrickFields;
use Awcodes\Mason\Brick;
use Filament\Actions\Action;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;
use RalphJSmit\Filament\MediaLibrary\Filament\Forms\Components\MediaPicker;

class PersonCardsBrick extends Brick
{
    public static function getId(): string
    {
        return 'person-cards';
    }

    public static function getLabel(): string
    {
        return __('bricks.names.person-cards');
    }

    public static function getIcon(): string|Heroicon|Htmlable|null
    {
        return Heroicon::OutlinedUserGroup;
    }

    public static function toHtml(array $config, ?array $data = null): ?string
    {
        return view('mason.bricks.person-cards.index', $config)->render();
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
                    TextInput::make("subtitle.{$locale}")
                        ->label(__('bricks.fields.subtitle')),
                ]),
                Repeater::make('people')
                    ->label(__('bricks.person_cards.people'))
                    ->schema([
                        MediaPicker::make('image')
                            ->label(__('bricks.fields.image')),
                        TranslatableBrickFields::group(fn (string $locale) => [
                            TextInput::make("name.{$locale}")
                                ->label(__('bricks.fields.name'))
                                ->required(),
                            TextInput::make("role.{$locale}")
                                ->label(__('bricks.fields.role')),
                            BrickRichEditor::make("description.{$locale}")
                                ->label(__('bricks.fields.description')),
                        ]),
                        TagsInput::make('tags')
                            ->label(__('bricks.fields.tags')),
                    ])
                    ->reorderable()
                    ->reorderableWithButtons()
                    ->cloneable()
                    ->collapsible(),
            ]);
    }
}
