<?php

namespace App\Mason\Bricks;

use App\Mason\Support\BrickRichEditor;
use App\Mason\Support\LinkPickerField;
use App\Mason\Support\TranslatableBrickFields;
use Awcodes\Mason\Brick;
use Filament\Actions\Action;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;
use RalphJSmit\Filament\MediaLibrary\Filament\Forms\Components\MediaPicker;

class FounderSpotlightBrick extends Brick
{
    public static function getId(): string
    {
        return 'founder-spotlight';
    }

    public static function getLabel(): string
    {
        return __('bricks.names.founder-spotlight');
    }

    public static function getIcon(): string|Heroicon|Htmlable|null
    {
        return Heroicon::OutlinedStar;
    }

    public static function toHtml(array $config, ?array $data = null): ?string
    {
        return view('mason.bricks.founder-spotlight.index', $config)->render();
    }

    public static function configureBrickAction(Action $action): Action
    {
        return $action
            ->slideOver()
            ->schema([
                MediaPicker::make('image')
                    ->label(__('bricks.fields.image')),
                TranslatableBrickFields::group(fn (string $locale) => [
                    TextInput::make("label.{$locale}")
                        ->label(__('bricks.fields.label')),
                    TextInput::make("name_line1.{$locale}")
                        ->label(__('bricks.founder.name_line1')),
                    TextInput::make("name_line2.{$locale}")
                        ->label(__('bricks.founder.name_line2')),
                    TextInput::make("subtitle.{$locale}")
                        ->label(__('bricks.fields.subtitle')),
                    BrickRichEditor::make("bio.{$locale}")
                        ->label(__('bricks.founder.bio')),
                    BrickRichEditor::make("bio2.{$locale}")
                        ->label(__('bricks.founder.bio2')),
                    LinkPickerField::make('cta_', $locale, null, 'cta_text', __('bricks.founder.cta_text')),
                ]),
                Repeater::make('stats')
                    ->label(__('bricks.founder.stats'))
                    ->schema([
                        TextInput::make('number')
                            ->label(__('bricks.fields.number')),
                        TranslatableBrickFields::group(fn (string $locale) => [
                            TextInput::make("label.{$locale}")
                                ->label(__('bricks.fields.label')),
                        ]),
                    ])
                    ->reorderable()
                    ->reorderableWithButtons()
                    ->cloneable()
                    ->collapsible(),
            ]);
    }
}
