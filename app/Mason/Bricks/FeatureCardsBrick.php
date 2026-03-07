<?php

namespace App\Mason\Bricks;

use Awcodes\Mason\Brick;
use Filament\Actions\Action;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Repeater\TableColumn;
use Filament\Forms\Components\TextInput;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;

class FeatureCardsBrick extends Brick
{
    public static function getId(): string
    {
        return 'feature-cards';
    }

    public static function getIcon(): string|Heroicon|Htmlable|null
    {
        return Heroicon::OutlinedSquares2x2;
    }

    public static function toHtml(array $config, ?array $data = null): ?string
    {
        return view('mason.bricks.feature-cards.index', $config)->render();
    }

    public static function configureBrickAction(Action $action): Action
    {
        return $action
            ->slideOver()
            ->schema([
                Repeater::make('cards')
                    ->table([
                        TableColumn::make('Icon'),
                        TableColumn::make('Title'),
                        TableColumn::make('Description'),
                    ])
                    ->schema([
                        TextInput::make('icon')
                            ->placeholder('e.g. heroicon-o-star'),
                        TextInput::make('title')
                            ->required(),
                        TextInput::make('description'),
                    ])
                    ->reorderable(),
            ]);
    }
}
