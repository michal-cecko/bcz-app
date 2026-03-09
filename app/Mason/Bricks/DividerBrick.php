<?php

namespace App\Mason\Bricks;

use App\Mason\Support\TranslatableBrickFields;
use Awcodes\Mason\Brick;
use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;

class DividerBrick extends Brick
{
    public static function getId(): string
    {
        return 'divider';
    }

    public static function getLabel(): string
    {
        return __('bricks.names.divider');
    }

    public static function getIcon(): string|Heroicon|Htmlable|null
    {
        return Heroicon::OutlinedMinus;
    }

    public static function toHtml(array $config, ?array $data = null): ?string
    {
        return view('mason.bricks.divider.index', $config)->render();
    }

    public static function configureBrickAction(Action $action): Action
    {
        return $action
            ->schema([
                TranslatableBrickFields::group(fn (string $locale) => [
                    TextInput::make("label.{$locale}")
                        ->label(__('bricks.fields.label'))
                        ->placeholder(__('bricks.divider.label_placeholder')),
                ]),
            ]);
    }
}
