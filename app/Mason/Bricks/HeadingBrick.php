<?php

namespace App\Mason\Bricks;

use App\Mason\Support\TranslatableBrickFields;
use Awcodes\Mason\Brick;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;

class HeadingBrick extends Brick
{
    public static function getId(): string
    {
        return 'heading';
    }

    public static function getLabel(): string
    {
        return __('bricks.names.heading');
    }

    public static function getIcon(): string|Heroicon|Htmlable|null
    {
        return Heroicon::OutlinedH1;
    }

    public static function toHtml(array $config, ?array $data = null): ?string
    {
        return view('mason.bricks.heading.index', $config)->render();
    }

    public static function configureBrickAction(Action $action): Action
    {
        return $action
            ->schema([
                Select::make('level')
                    ->label(__('bricks.heading.level'))
                    ->options([
                        'h2' => __('bricks.heading.h2'),
                        'h3' => __('bricks.heading.h3'),
                        'h4' => __('bricks.heading.h4'),
                    ])
                    ->default('h2')
                    ->required(),
                TranslatableBrickFields::group(fn (string $locale) => [
                    TextInput::make("text.{$locale}")
                        ->label(__('bricks.fields.text'))
                        ->required(),
                ]),
            ]);
    }
}
