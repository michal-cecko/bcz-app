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
use Guava\IconPicker\Forms\Components\IconPicker;
use Illuminate\Contracts\Support\Htmlable;
use Throwable;

class CompetitionCtaBrick extends Brick
{
    public static function getId(): string
    {
        return 'competition-cta';
    }

    public static function getLabel(): string
    {
        return 'CTA výrazné';
    }

    public static function getIcon(): string|Heroicon|Htmlable|null
    {
        return Heroicon::OutlinedMegaphone;
    }

    /** @throws Throwable */
    public static function toHtml(array $config, ?array $data = null): ?string
    {
        return view('mason.bricks.competition-cta.index', $config)->render();
    }

    public static function configureBrickAction(Action $action): Action
    {
        return $action
            ->slideOver()
            ->schema([
                TranslatableBrickFields::group(fn (string $locale) => [
                    TextInput::make("title.{$locale}")
                        ->label("Nadpis ({$locale})")
                        ->required(),
                    BrickRichEditor::make("description.{$locale}")
                        ->label("Popis ({$locale})"),
                    LinkPickerField::make('button_', $locale, 'Hlavné tlačidlo', 'button_text', 'Text tlačidla'),
                ]),
                IconPicker::make('button_icon')
                    ->label('Ikona hlavného tlačidla')
                    ->sets(['heroicons'])
                    ->columns(3),
                Fieldset::make('Sekundárne tlačidlo')
                    ->schema([
                        TranslatableBrickFields::group(fn (string $locale) => [
                            TextInput::make("secondary_text.{$locale}")
                                ->label('Text tlačidla'),
                            LinkPickerField::make('secondary_', $locale),
                        ]),
                    ]),
                Select::make('background_color')
                    ->label('Farba pozadia')
                    ->options(self::colorOptions())
                    ->default('#FF2D2D')
                    ->native(false)
                    ->allowHtml(),
            ]);
    }

    private static function colorOptions(): array
    {
        $colors = [
            '#FF2D2D' => 'Červená (BCZ)',
            '#CC0000' => 'Tmavočervená',
            '#9333EA' => 'Fialová',
            '#2563EB' => 'Modrá',
            '#059669' => 'Zelená',
            '#0A0A0A' => 'Tmavá',
            '#111111' => 'Čierna',
        ];

        $options = [];
        foreach ($colors as $hex => $label) {
            $options[$hex] = '<span style="display:inline-flex;align-items:center;gap:8px;"><span style="width:14px;height:14px;border-radius:3px;background:'.$hex.';display:inline-block;"></span> '.$label.'</span>';
        }

        return $options;
    }
}
