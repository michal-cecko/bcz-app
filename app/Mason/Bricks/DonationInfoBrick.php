<?php

namespace App\Mason\Bricks;

use App\Mason\Support\LinkPickerField;
use App\Mason\Support\TranslatableBrickFields;
use Awcodes\Mason\Brick;
use Filament\Actions\Action;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Tabs;
use Filament\Support\Icons\Heroicon;
use Guava\IconPicker\Forms\Components\IconPicker;
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
                                        TranslatableBrickFields::group(fn (string $locale) => [
                                            TextInput::make("label.{$locale}")
                                                ->label('Označenie'),
                                            TextInput::make("value.{$locale}")
                                                ->label('Hodnota'),
                                        ]),
                                    ])
                                    ->reorderable()
                                    ->collapsible()
                                    ->defaultItems(0)
                                    ->columnSpanFull(),
                                TranslatableBrickFields::group(fn (string $locale) => [
                                    TextInput::make("qr_title.{$locale}")
                                        ->label('Nadpis QR sekcie'),
                                    TextInput::make("qr_description.{$locale}")
                                        ->label('Popis QR sekcie'),
                                ]),
                                Fieldset::make('QR kód a platobné údaje')
                                    ->schema([
                                        TranslatableBrickFields::group(fn (string $locale) => [
                                            TextInput::make("iban.{$locale}")
                                                ->label('IBAN')
                                                ->helperText('Používa sa na kopírovanie aj generovanie QR kódu.'),
                                            TextInput::make("account_number.{$locale}")
                                                ->label('Číslo účtu (voliteľné)')
                                                ->helperText('Český formát, napr. 1503666677/5500. Ak je vyplnené, použije sa pre QR Platba.'),
                                            TextInput::make("qr_recipient_name.{$locale}")
                                                ->label('Meno príjemcu'),
                                            TextInput::make("qr_variable_symbol.{$locale}")
                                                ->label('Variabilný symbol (voliteľný)'),
                                            TextInput::make("qr_note.{$locale}")
                                                ->label('Poznámka platby (voliteľná)')
                                                ->helperText('Max 140 znakov (Pay by Square) / 60 znakov (QR Platba).')
                                                ->maxLength(140),
                                            Select::make("qr_format.{$locale}")
                                                ->label('Formát QR kódu')
                                                ->options([
                                                    'pay_by_square' => 'Pay by Square — SK (EUR)',
                                                    'qr_platba' => 'QR Platba / SPD — CZ (CZK)',
                                                ])
                                                ->default('pay_by_square')
                                                ->native(false),
                                        ]),
                                    ])
                                    ->columns(1),
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
                                        Select::make('color')
                                            ->label('Farba ikony')
                                            ->options([
                                                '#FF2D2D' => 'Červená',
                                                '#3B82F6' => 'Modrá',
                                                '#22C55E' => 'Zelená',
                                                '#8B5CF6' => 'Fialová',
                                                '#F59E0B' => 'Žltá',
                                                '#EC4899' => 'Ružová',
                                                '#14B8A6' => 'Tyrkysová',
                                                '#F97316' => 'Oranžová',
                                            ])
                                            ->default('#FF2D2D')
                                            ->native(false),
                                        TranslatableBrickFields::group(fn (string $locale) => [
                                            TextInput::make("title.{$locale}")
                                                ->label('Nadpis')
                                                ->required(),
                                            TextInput::make("description.{$locale}")
                                                ->label('Popis')
                                                ->required(),
                                        ]),
                                    ])
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
                                        LinkPickerField::make('tax_', label: 'Odkaz na 2% stránku')
                                            ->columnSpanFull(),
                                        TranslatableBrickFields::group(fn (string $locale) => [
                                            TextInput::make("tax_button_text.{$locale}")
                                                ->label('Text tlačidla'),
                                        ]),
                                    ]),
                                Fieldset::make('Kontaktná karta')
                                    ->columns(1)
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
