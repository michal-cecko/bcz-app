<?php

namespace App\Mason\Bricks;

use Awcodes\Mason\Brick;
use Filament\Actions\Action;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;

class QuoteBrick extends Brick
{
    public static function getId(): string
    {
        return 'quote';
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
                Textarea::make('quote')
                    ->required()
                    ->rows(3),
                TextInput::make('attribution'),
            ]);
    }
}
