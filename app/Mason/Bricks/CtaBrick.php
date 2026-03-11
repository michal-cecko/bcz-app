<?php

namespace App\Mason\Bricks;

use App\Mason\Support\BrickRichEditor;
use App\Mason\Support\LinkPickerField;
use App\Mason\Support\TranslatableBrickFields;
use Awcodes\Mason\Brick;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Fieldset;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;

class CtaBrick extends Brick
{
    public static function getId(): string
    {
        return 'cta';
    }

    public static function getLabel(): string
    {
        return __('bricks.names.cta');
    }

    public static function getIcon(): string|Heroicon|Htmlable|null
    {
        return Heroicon::OutlinedMegaphone;
    }

    public static function toHtml(array $config, ?array $data = null): ?string
    {
        return view('mason.bricks.cta.index', $config)->render();
    }

    public static function configureBrickAction(Action $action): Action
    {
        return $action
            ->slideOver()
            ->schema([
                TranslatableBrickFields::group(fn (string $locale) => [
                    TextInput::make("title.{$locale}")
                        ->label(__('bricks.fields.title'))
                        ->required(),
                    BrickRichEditor::make("description.{$locale}")
                        ->label(__('bricks.fields.description')),
                    LinkPickerField::make('button_', $locale, null, 'button_text', __('bricks.cta.button_text')),
                ]),
                Select::make('button_icon')
                    ->label('Ikona primárneho tlačidla')
                    ->options([
                        'heroicon-o-heart' => 'Srdce',
                        'heroicon-o-arrow-right' => 'Šípka',
                        'heroicon-o-rocket-launch' => 'Raketa',
                        'heroicon-o-star' => 'Hviezda',
                        'heroicon-o-bolt' => 'Blesk',
                        'heroicon-o-gift' => 'Darček',
                        'heroicon-o-hand-raised' => 'Ruka',
                        'heroicon-o-phone' => 'Telefón',
                    ])
                    ->native(false)
                    ->placeholder('Žiadna ikona'),
                Fieldset::make('Sekundárne tlačidlo')
                    ->schema([
                        TranslatableBrickFields::group(fn (string $locale) => [
                            TextInput::make("secondary_text.{$locale}")
                                ->label('Text tlačidla'),
                            LinkPickerField::make('secondary_', $locale),
                        ]),
                    ]),
                Select::make('background_color')
                    ->label(__('bricks.cta.background_color'))
                    ->options([
                        '#0A0A0A' => 'Tmavá (BCZ Dark)',
                        '#dc2626' => 'Červená (BCZ Red)',
                        '#1f2937' => 'Šedá (Gray 800)',
                        '#111827' => 'Čierna (Gray 900)',
                        '#0f172a' => 'Tmavomodrá (Slate 900)',
                        '#14532d' => 'Zelená (Green 900)',
                        '#7c2d12' => 'Oranžová (Orange 900)',
                    ])
                    ->default('#0A0A0A')
                    ->native(false),
            ]);
    }
}
