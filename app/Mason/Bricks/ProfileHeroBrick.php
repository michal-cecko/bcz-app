<?php

namespace App\Mason\Bricks;

use App\Mason\Support\TranslatableBrickFields;
use Awcodes\Mason\Brick;
use Filament\Actions\Action;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;
use RalphJSmit\Filament\MediaLibrary\Filament\Forms\Components\MediaPicker;

class ProfileHeroBrick extends Brick
{
    public static function getId(): string
    {
        return 'profile-hero';
    }

    public static function getLabel(): string
    {
        return __('bricks.names.profile-hero');
    }

    public static function getIcon(): string|Heroicon|Htmlable|null
    {
        return Heroicon::OutlinedPhoto;
    }

    public static function toHtml(array $config, ?array $data = null): ?string
    {
        return view('mason.bricks.profile-hero.index', $config)->render();
    }

    public static function configureBrickAction(Action $action): Action
    {
        return $action
            ->slideOver()
            ->schema([
                MediaPicker::make('background_image')
                    ->label('Obrázok pozadia'),
                TranslatableBrickFields::group(fn (string $locale) => [
                    TextInput::make("badge.{$locale}")
                        ->label('Badge'),
                    TextInput::make("title.{$locale}")
                        ->label('Titulok')
                        ->required(),
                    TextInput::make("subtitle.{$locale}")
                        ->label('Podtitulok'),
                ]),
                Repeater::make('breadcrumb')
                    ->schema([
                        TranslatableBrickFields::group(fn (string $locale) => [
                            TextInput::make("text.{$locale}")
                                ->label('Text')
                                ->required(),
                        ]),
                        TextInput::make('url')
                            ->label('URL'),
                    ])
                    ->addActionLabel('Pridať do cesty')
                    ->defaultItems(0)
                    ->reorderable()
                    ->reorderableWithButtons()
                    ->cloneable()
                    ->collapsible(),
            ]);
    }
}
