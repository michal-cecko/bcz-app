<?php

namespace App\Mason\EmailBricks;

use Awcodes\Mason\Brick;
use Filament\Actions\Action;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;

class EmailCalloutBrick extends Brick
{
    public static function getId(): string
    {
        return 'email-callout';
    }

    public static function getLabel(): string
    {
        return 'Upozornenie';
    }

    public static function getIcon(): string|Heroicon|Htmlable|null
    {
        return Heroicon::OutlinedExclamationCircle;
    }

    public static function toHtml(array $config, ?array $data = null): ?string
    {
        return view('mason.email-bricks.email-callout.index', $config)->render();
    }

    public static function configureBrickAction(Action $action): Action
    {
        return $action
            ->slideOver()
            ->schema([
                RichEditor::make('content')
                    ->label('Obsah')
                    ->toolbarButtons([
                        'bold',
                        'italic',
                        'link',
                        'bulletList',
                    ])
                    ->required(),
                Select::make('color')
                    ->label('Farba')
                    ->options([
                        'blue' => '<span style="display:inline-flex;align-items:center;gap:8px;"><span style="width:14px;height:14px;border-radius:3px;background:#2563eb;display:inline-block;"></span> Informácia</span>',
                        'green' => '<span style="display:inline-flex;align-items:center;gap:8px;"><span style="width:14px;height:14px;border-radius:3px;background:#16a34a;display:inline-block;"></span> Úspech</span>',
                        'yellow' => '<span style="display:inline-flex;align-items:center;gap:8px;"><span style="width:14px;height:14px;border-radius:3px;background:#eab308;display:inline-block;"></span> Varovanie</span>',
                        'red' => '<span style="display:inline-flex;align-items:center;gap:8px;"><span style="width:14px;height:14px;border-radius:3px;background:#dc2626;display:inline-block;"></span> Chyba</span>',
                    ])
                    ->searchable()
                    ->allowHtml()
                    ->default('blue')
                    ->required(),
            ]);
    }
}
