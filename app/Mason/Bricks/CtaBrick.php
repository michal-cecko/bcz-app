<?php

namespace App\Mason\Bricks;

use App\Mason\Support\BrickRichEditor;
use App\Mason\Support\LinkPickerField;
use App\Mason\Support\TranslatableBrickFields;
use Awcodes\Mason\Brick;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
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
                Select::make('background_color')
                    ->label(__('bricks.cta.background_color'))
                    ->options([
                        '#dc2626' => 'Červená (BCZ Red)',
                        '#1a1a2e' => 'Tmavá (BCZ Dark)',
                        '#1f2937' => 'Šedá (Gray 800)',
                        '#111827' => 'Čierna (Gray 900)',
                        '#0f172a' => 'Tmavomodrá (Slate 900)',
                        '#14532d' => 'Zelená (Green 900)',
                        '#7c2d12' => 'Oranžová (Orange 900)',
                    ])
                    ->default('#1f2937')
                    ->native(false),
            ]);
    }
}
