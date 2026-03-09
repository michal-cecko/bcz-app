<?php

namespace App\Mason\Bricks;

use App\Mason\Support\BrickRichEditor;
use App\Mason\Support\TranslatableBrickFields;
use Awcodes\Mason\Brick;
use Filament\Actions\Action;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;

class RichTextBrick extends Brick
{
    public static function getId(): string
    {
        return 'rich-text';
    }

    public static function getLabel(): string
    {
        return __('bricks.names.rich-text');
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
                TranslatableBrickFields::group(fn (string $locale) => [
                    BrickRichEditor::make("content.{$locale}")
                        ->label(__('bricks.fields.content'))
                        ->required(),
                ]),
            ]);
    }
}
