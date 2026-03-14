<?php

namespace App\Mason\Bricks;

use App\Mason\Support\TranslatableBrickFields;
use Awcodes\Mason\Brick;
use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;

class CenteredHeroBrick extends Brick
{
    public static function getId(): string
    {
        return 'centered-hero';
    }

    public static function getLabel(): string
    {
        return __('bricks.names.centered-hero');
    }

    public static function getIcon(): string|Heroicon|Htmlable|null
    {
        return Heroicon::OutlinedStar;
    }

    public static function toHtml(array $config, ?array $data = null): ?string
    {
        return view('mason.bricks.centered-hero.index', $config)->render();
    }

    public static function configureBrickAction(Action $action): Action
    {
        return $action
            ->slideOver()
            ->schema([
                TranslatableBrickFields::group(fn (string $locale) => [
                    TextInput::make("badge.{$locale}")
                        ->label('Badge'),
                    TextInput::make("title.{$locale}")
                        ->label('Titulok')
                        ->required(),
                    TextInput::make("subtitle.{$locale}")
                        ->label('Podtitulok'),
                    TextInput::make("highlight.{$locale}")
                        ->label('Zvýraznený text'),
                ]),
            ]);
    }
}
