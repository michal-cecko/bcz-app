<?php

namespace App\Mason\Bricks;

use App\Mason\Support\LinkPickerField;
use App\Mason\Support\TranslatableBrickFields;
use Awcodes\Mason\Brick;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;

class HeroBrick extends Brick
{
    public static function getId(): string
    {
        return 'hero';
    }

    public static function getLabel(): string
    {
        return __('bricks.names.hero');
    }

    public static function getIcon(): string|Heroicon|Htmlable|null
    {
        return Heroicon::OutlinedPhoto;
    }

    public static function toHtml(array $config, ?array $data = null): ?string
    {
        return view('mason.bricks.hero.index', $config)->render();
    }

    public static function configureBrickAction(Action $action): Action
    {
        return $action
            ->slideOver()
            ->schema([
                Select::make('layout')
                    ->label(__('bricks.hero.layout'))
                    ->options([
                        'left' => __('bricks.hero.layout_left'),
                        'centered' => __('bricks.hero.layout_centered'),
                    ])
                    ->default('left'),
                FileUpload::make('background_image')
                    ->image()
                    ->disk('public')
                    ->directory('bricks')
                    ->visibility('public')
                    ->label(__('bricks.hero.background_image')),
                TranslatableBrickFields::group(fn (string $locale) => [
                    TextInput::make("badge.{$locale}")
                        ->label(__('bricks.hero.badge')),
                    TextInput::make("title.{$locale}")
                        ->label(__('bricks.fields.title'))
                        ->required(),
                    TextInput::make("title_accent.{$locale}")
                        ->label(__('bricks.hero.title_accent')),
                    TextInput::make("subtitle.{$locale}")
                        ->label(__('bricks.fields.subtitle')),
                    TextInput::make("scroll_text.{$locale}")
                        ->label(__('bricks.hero.scroll_text')),
                    LinkPickerField::make('cta_', $locale, __('bricks.hero.cta_url'), 'cta_text', __('bricks.hero.cta_text')),
                    LinkPickerField::make('secondary_cta_', $locale, __('bricks.hero.secondary_cta_url'), 'secondary_cta_text', __('bricks.hero.secondary_cta_text')),
                ]),
                Repeater::make('breadcrumb')
                    ->label(__('bricks.hero.breadcrumb'))
                    ->schema([
                        TranslatableBrickFields::group(fn (string $locale) => [
                            TextInput::make("text.{$locale}")
                                ->label(__('bricks.fields.text'))
                                ->required(),
                        ]),
                        TextInput::make('url')
                            ->label(__('bricks.fields.url')),
                    ])
                    ->addActionLabel(__('bricks.hero.breadcrumb_add'))
                    ->defaultItems(0)
                    ->reorderable()
                    ->reorderableWithButtons()
                    ->cloneable()
                    ->collapsible(),
            ]);
    }
}
