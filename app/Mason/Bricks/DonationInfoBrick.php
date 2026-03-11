<?php

namespace App\Mason\Bricks;

use App\Mason\Support\LinkPickerField;
use App\Mason\Support\TranslatableBrickFields;
use Awcodes\Mason\Brick;
use Filament\Actions\Action;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Tabs;
use Filament\Support\Icons\Heroicon;
use Guava\FilamentIconPicker\Forms\IconPicker;
use Illuminate\Contracts\Support\Htmlable;

class DonationInfoBrick extends Brick
{
    public static function getId(): string
    {
        return 'donation-info';
    }

    public static function getLabel(): string
    {
        return __('bricks.names.donation-info');
    }

    public static function getIcon(): string|Heroicon|Htmlable|null
    {
        return Heroicon::OutlinedHeart;
    }

    public static function toHtml(array $config, ?array $data = null): ?string
    {
        return view('mason.bricks.donation-info.index', $config)->render();
    }

    public static function configureBrickAction(Action $action): Action
    {
        return $action
            ->slideOver()
            ->schema([
                Tabs::make('Sekcie')
                    ->tabs([
                        Tabs\Tab::make('Bankové údaje')
                            ->schema([
                                TranslatableBrickFields::group(fn (string $locale) => [
                                    TextInput::make("bank_title.{$locale}")
                                        ->label('Nadpis bankového bloku'),
                                ]),
                                Repeater::make('bank_rows')
                                    ->label('Riadky bankových údajov')
                                    ->schema([
                                        TextInput::make('label')
                                            ->label('Označenie')
                                            ->required(),
                                        TextInput::make('value')
                                            ->label('Hodnota')
                                            ->required(),
                                    ])
                                    ->columns(2)
                                    ->reorderable()
                                    ->defaultItems(0)
                                    ->columnSpanFull(),
                                TranslatableBrickFields::group(fn (string $locale) => [
                                    TextInput::make("qr_title.{$locale}")
                                        ->label('Nadpis QR sekcie'),
                                    TextInput::make("qr_description.{$locale}")
                                        ->label('Popis QR sekcie'),
                                ]),
                                TextInput::make('iban_copy')
                                    ->label('IBAN na kopírovanie'),
                            ]),
                        Tabs\Tab::make('Využitie darov')
                            ->schema([
                                TranslatableBrickFields::group(fn (string $locale) => [
                                    TextInput::make("usage_title.{$locale}")
                                        ->label('Nadpis'),
                                    TextInput::make("usage_description.{$locale}")
                                        ->label('Popis'),
                                ]),
                                Repeater::make('usage_items')
                                    ->label('Položky')
                                    ->schema([
                                        IconPicker::make('icon')
                                            ->label('Ikona'),
                                        TextInput::make('color')
                                            ->label('Farba ikony')
                                            ->placeholder('#FF2D2D'),
                                        TextInput::make('title')
                                            ->label('Nadpis')
                                            ->required(),
                                        TextInput::make('description')
                                            ->label('Popis')
                                            ->required(),
                                    ])
                                    ->columns(2)
                                    ->reorderable()
                                    ->collapsible()
                                    ->defaultItems(0)
                                    ->columnSpanFull(),
                            ]),
                        Tabs\Tab::make('Bočný panel')
                            ->schema([
                                Fieldset::make('Karta 2% z dane')
                                    ->schema([
                                        TranslatableBrickFields::group(fn (string $locale) => [
                                            TextInput::make("tax_title.{$locale}")
                                                ->label('Nadpis'),
                                            TextInput::make("tax_description.{$locale}")
                                                ->label('Popis'),
                                        ]),
                                        LinkPickerField::make('tax_', label: 'Odkaz na 2% stránku'),
                                        TranslatableBrickFields::group(fn (string $locale) => [
                                            TextInput::make("tax_button_text.{$locale}")
                                                ->label('Text tlačidla'),
                                        ]),
                                    ]),
                                Fieldset::make('Kontaktná karta')
                                    ->schema([
                                        TranslatableBrickFields::group(fn (string $locale) => [
                                            TextInput::make("contact_title.{$locale}")
                                                ->label('Nadpis'),
                                            TextInput::make("contact_description.{$locale}")
                                                ->label('Popis'),
                                        ]),
                                        TextInput::make('contact_email')
                                            ->label('E-mail'),
                                        TextInput::make('contact_phone')
                                            ->label('Telefón'),
                                        TextInput::make('contact_address')
                                            ->label('Adresa'),
                                    ]),
                            ]),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
