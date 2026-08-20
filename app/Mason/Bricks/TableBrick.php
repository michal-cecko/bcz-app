<?php

namespace App\Mason\Bricks;

use App\Mason\Support\TranslatableBrickFields;
use Awcodes\Mason\Brick;
use Filament\Actions\Action;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;

class TableBrick extends Brick
{
    public static function getId(): string
    {
        return 'table';
    }

    public static function getLabel(): string
    {
        return __('bricks.names.table');
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
                    ->label(__('bricks.table.headers'))
                    ->schema([
                        TranslatableBrickFields::group(fn (string $locale) => [
                            TextInput::make("label.{$locale}")
                                ->label(__('bricks.fields.label'))
                                ->required(),
                        ]),
                    ])
                    ->reorderable()
                    ->reorderableWithButtons()
                    ->cloneable()
                    ->collapsible(),
                Repeater::make('rows')
                    ->label(__('bricks.table.rows'))
                    ->addActionLabel('Pridať riadok')
                    ->schema([
                        Repeater::make('cells')
                            ->label(__('bricks.table.cells'))
                            ->addActionLabel('Pridať hodnotu')
                            ->schema([
                                TranslatableBrickFields::group(fn (string $locale) => [
                                    TextInput::make("value.{$locale}")
                                        ->label(__('bricks.fields.value')),
                                ]),
                            ])
                            ->reorderable()
                            ->reorderableWithButtons()
                            ->cloneable()
                            ->collapsible()
                            ->columnSpanFull(),
                    ])
                    ->collapsible()
                    ->reorderable()
                    ->reorderableWithButtons()
                    ->cloneable(),
            ]);
    }
}
