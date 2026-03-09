<?php

namespace App\Mason\Bricks;

use App\Mason\Support\BrickRichEditor;
use App\Mason\Support\TranslatableBrickFields;
use Awcodes\Mason\Brick;
use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;

class QuoteBrick extends Brick
{
    public static function getId(): string
    {
        return 'quote';
    }

    public static function getLabel(): string
    {
        return __('bricks.names.quote');
    }

    public static function getIcon(): string|Heroicon|Htmlable|null
    {
        return Heroicon::OutlinedChatBubbleBottomCenterText;
    }

    public static function toHtml(array $config, ?array $data = null): ?string
    {
        return view('mason.bricks.quote.index', $config)->render();
    }

    public static function configureBrickAction(Action $action): Action
    {
        return $action
            ->slideOver()
            ->schema([
                TranslatableBrickFields::group(fn (string $locale) => [
                    BrickRichEditor::make("quote.{$locale}")
                        ->label(__('bricks.names.quote'))
                        ->required(),
                    TextInput::make("attribution.{$locale}")
                        ->label(__('bricks.fields.name')),
                ]),
            ]);
    }
}
