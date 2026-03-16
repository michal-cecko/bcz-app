<?php

namespace App\Mason\EmailBricks;

use Awcodes\Mason\Brick;
use Filament\Actions\Action;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;

class EmailRichTextBrick extends Brick
{
    public static function getId(): string
    {
        return 'email-rich-text';
    }

    public static function getLabel(): string
    {
        return 'Text';
    }

    public static function getIcon(): string|Heroicon|Htmlable|null
    {
        return Heroicon::OutlinedDocumentText;
    }

    public static function toHtml(array $config, ?array $data = null): ?string
    {
        return view('mason.email-bricks.email-rich-text.index', $config)->render();
    }

    public static function configureBrickAction(Action $action): Action
    {
        return $action
            ->slideOver()
            ->schema([
                Select::make('alignment')
                    ->label('Zarovnanie')
                    ->options([
                        'left' => 'Vľavo',
                        'center' => 'Na stred',
                        'right' => 'Vpravo',
                    ])
                    ->default('left'),
                RichEditor::make('content')
                    ->label('Obsah')
                    ->toolbarButtons([
                        'bold',
                        'italic',
                        'underline',
                        'link',
                        'h2',
                        'h3',
                        'bulletList',
                        'orderedList',
                    ])
                    ->required(),
            ]);
    }
}
