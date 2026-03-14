<?php

namespace App\Mason\Bricks;

use App\Mason\Support\TranslatableBrickFields;
use Awcodes\Mason\Brick;
use Filament\Actions\Action;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;

class DetailsCardBrick extends Brick
{
    public static function getId(): string
    {
        return 'details-card';
    }

    public static function getLabel(): string
    {
        return __('bricks.names.details-card');
    }

    public static function getIcon(): string|Heroicon|Htmlable|null
    {
        return Heroicon::OutlinedClipboardDocumentList;
    }

    public static function toHtml(array $config, ?array $data = null): ?string
    {
        return view('mason.bricks.details-card.index', $config)->render();
    }

    public static function configureBrickAction(Action $action): Action
    {
        return $action
            ->slideOver()
            ->schema([
                TranslatableBrickFields::group(fn (string $locale) => [
                    TextInput::make("title.{$locale}")
                        ->label('Titulok')
                        ->required(),
                    TextInput::make("subtitle.{$locale}")
                        ->label('Podtitulok'),
                ]),
                Repeater::make('rows')
                    ->schema([
                        TranslatableBrickFields::group(fn (string $locale) => [
                            TextInput::make("label.{$locale}")
                                ->label('Označenie')
                                ->required(),
                            TextInput::make("value.{$locale}")
                                ->label('Hodnota')
                                ->required(),
                        ]),
                        Toggle::make('highlight')
                            ->label('Zvýrazniť hodnotu'),
                    ])
                    ->addActionLabel('Pridať riadok')
                    ->reorderable()
                    ->collapsible(),
                Toggle::make('show_copy_button')
                    ->label('Zobraziť tlačidlo kopírovania')
                    ->default(true),
            ]);
    }
}
