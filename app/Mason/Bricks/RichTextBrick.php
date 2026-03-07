<?php

namespace App\Mason\Bricks;

use Awcodes\Mason\Brick;
use Awcodes\RicherEditor\RicherEditor;
use Filament\Actions\Action;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;

class RichTextBrick extends Brick
{
    public static function getId(): string
    {
        return 'rich-text';
    }

    public static function getIcon(): string|Heroicon|Htmlable|null
    {
        return Heroicon::OutlinedDocumentText;
    }

    public static function toHtml(array $config, ?array $data = null): ?string
    {
        return view('mason.bricks.rich-text.index', $config)->render();
    }

    public static function configureBrickAction(Action $action): Action
    {
        return $action
            ->slideOver()
            ->schema([
                RicherEditor::make('content')
                    ->required(),
            ]);
    }
}
