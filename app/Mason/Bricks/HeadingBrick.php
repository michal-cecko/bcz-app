<?php

namespace App\Mason\Bricks;

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
                TextInput::make('text')
                    ->required(),
                Select::make('level')
                    ->options([
                        'h2' => 'Heading 2',
                        'h3' => 'Heading 3',
                        'h4' => 'Heading 4',
                    ])
                    ->default('h2')
                    ->required(),
            ]);
    }
}
