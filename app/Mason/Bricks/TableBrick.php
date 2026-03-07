<?php

namespace App\Mason\Bricks;

use Awcodes\Mason\Brick;
use Filament\Actions\Action;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Repeater\TableColumn;
use Filament\Forms\Components\TextInput;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;

class TableBrick extends Brick
{
    public static function getId(): string
    {
        return 'table';
    }

    public static function getIcon(): string|Heroicon|Htmlable|null
    {
        return Heroicon::OutlinedTableCells;
    }

    public static function toHtml(array $config, ?array $data = null): ?string
    {
        return view('mason.bricks.table.index', $config)->render();
    }

    public static function configureBrickAction(Action $action): Action
    {
        return $action
            ->slideOver()
            ->schema([
                Repeater::make('headers')
                    ->table([
                        TableColumn::make('Label'),
                    ])
                    ->schema([
                        TextInput::make('label')
                            ->required(),
                    ])
                    ->reorderable(),
                Repeater::make('rows')
                    ->schema([
                        Repeater::make('cells')
                            ->table([
                                TableColumn::make('Value'),
                            ])
                            ->schema([
                                TextInput::make('value'),
                            ])
                            ->columnSpanFull(),
                    ])
                    ->collapsible()
                    ->reorderable(),
            ]);
    }
}
