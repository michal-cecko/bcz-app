<?php

namespace App\Mason\Bricks;

use Awcodes\Mason\Brick;
use Filament\Actions\Action;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Repeater\TableColumn;
use Filament\Forms\Components\TextInput;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;

class StatsBrick extends Brick
{
    public static function getId(): string
    {
        return 'stats';
    }

    public static function getIcon(): string|Heroicon|Htmlable|null
    {
        return Heroicon::OutlinedChartBar;
    }

    public static function toHtml(array $config, ?array $data = null): ?string
    {
        return view('mason.bricks.stats.index', $config)->render();
    }

    public static function configureBrickAction(Action $action): Action
    {
        return $action
            ->slideOver()
            ->schema([
                Repeater::make('items')
                    ->table([
                        TableColumn::make('Number'),
                        TableColumn::make('Label'),
                        TableColumn::make('Icon'),
                    ])
                    ->schema([
                        TextInput::make('number')
                            ->required(),
                        TextInput::make('label')
                            ->required(),
                        TextInput::make('icon')
                            ->placeholder('e.g. heroicon-o-users'),
                    ])
                    ->reorderable(),
            ]);
    }
}
