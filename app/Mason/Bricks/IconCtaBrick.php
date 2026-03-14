<?php

namespace App\Mason\Bricks;

use App\Mason\Support\LinkPickerField;
use App\Mason\Support\TranslatableBrickFields;
use Awcodes\Mason\Brick;
use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Support\Icons\Heroicon;
use Guava\IconPicker\Forms\Components\IconPicker;
use Illuminate\Contracts\Support\Htmlable;

class IconCtaBrick extends Brick
{
    public static function getId(): string
    {
        return 'icon-cta';
    }

    public static function getLabel(): string
    {
        return __('bricks.names.icon-cta');
    }

    public static function getIcon(): string|Heroicon|Htmlable|null
    {
        return Heroicon::OutlinedRocketLaunch;
    }

    public static function toHtml(array $config, ?array $data = null): ?string
    {
        return view('mason.bricks.icon-cta.index', $config)->render();
    }

    public static function configureBrickAction(Action $action): Action
    {
        return $action
            ->slideOver()
            ->schema([
                IconPicker::make('icon')
                    ->label('Ikona')
                    ->sets(['heroicons'])
                    ->columns(3),
                TranslatableBrickFields::group(fn (string $locale) => [
                    TextInput::make("icon_text.{$locale}")
                        ->label('Text ikony (alternatíva k ikone)'),
                    TextInput::make("title.{$locale}")
                        ->label('Titulok')
                        ->required(),
                    TextInput::make("description.{$locale}")
                        ->label('Popis'),
                    TextInput::make("primary_button_text.{$locale}")
                        ->label('Primárne tlačidlo'),
                    TextInput::make("secondary_button_text.{$locale}")
                        ->label('Sekundárne tlačidlo'),
                ]),
                LinkPickerField::make('primary_button_', label: 'Odkaz primárneho tlačidla'),
                LinkPickerField::make('secondary_button_', label: 'Odkaz sekundárneho tlačidla'),
            ]);
    }
}
