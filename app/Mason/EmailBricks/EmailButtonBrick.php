<?php

namespace App\Mason\EmailBricks;

use App\Mason\Support\LinkPickerField;
use Awcodes\Mason\Brick;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;

class EmailButtonBrick extends Brick
{
    public static function getId(): string
    {
        return 'email-button';
    }

    public static function getLabel(): string
    {
        return 'Tlačidlo';
    }

    public static function getIcon(): string|Heroicon|Htmlable|null
    {
        return Heroicon::OutlinedCursorArrowRays;
    }

    public static function toHtml(array $config, ?array $data = null): ?string
    {
        return view('mason.email-bricks.email-button.index', $config)->render();
    }

    public static function configureBrickAction(Action $action): Action
    {
        return $action
            ->schema([
                LinkPickerField::make(
                    prefix: 'button_',
                    label: 'Odkaz',
                    textFieldName: 'text',
                    textFieldLabel: 'Text tlačidla',
                ),
                Select::make('alignment')
                    ->label('Zarovnanie')
                    ->options([
                        'left' => 'Vľavo',
                        'center' => 'Na stred',
                        'right' => 'Vpravo',
                    ])
                    ->default('left'),
                Select::make('color')
                    ->label('Farba')
                    ->options([
                        'primary' => '<span style="display:inline-flex;align-items:center;gap:8px;"><span style="width:14px;height:14px;border-radius:3px;background:#2563eb;display:inline-block;"></span> Modrá</span>',
                        'success' => '<span style="display:inline-flex;align-items:center;gap:8px;"><span style="width:14px;height:14px;border-radius:3px;background:#16a34a;display:inline-block;"></span> Zelená</span>',
                        'danger' => '<span style="display:inline-flex;align-items:center;gap:8px;"><span style="width:14px;height:14px;border-radius:3px;background:#dc2626;display:inline-block;"></span> Červená</span>',
                        'warning' => '<span style="display:inline-flex;align-items:center;gap:8px;"><span style="width:14px;height:14px;border-radius:3px;background:#ea580c;display:inline-block;"></span> Oranžová</span>',
                        'purple' => '<span style="display:inline-flex;align-items:center;gap:8px;"><span style="width:14px;height:14px;border-radius:3px;background:#9333ea;display:inline-block;"></span> Fialová</span>',
                        'pink' => '<span style="display:inline-flex;align-items:center;gap:8px;"><span style="width:14px;height:14px;border-radius:3px;background:#db2777;display:inline-block;"></span> Ružová</span>',
                        'indigo' => '<span style="display:inline-flex;align-items:center;gap:8px;"><span style="width:14px;height:14px;border-radius:3px;background:#4f46e5;display:inline-block;"></span> Indigo</span>',
                        'teal' => '<span style="display:inline-flex;align-items:center;gap:8px;"><span style="width:14px;height:14px;border-radius:3px;background:#0d9488;display:inline-block;"></span> Tyrkysová</span>',
                        'gray' => '<span style="display:inline-flex;align-items:center;gap:8px;"><span style="width:14px;height:14px;border-radius:3px;background:#4b5563;display:inline-block;"></span> Sivá</span>',
                    ])
                    ->searchable()
                    ->allowHtml()
                    ->default('primary')
                    ->required(),
            ]);
    }
}
