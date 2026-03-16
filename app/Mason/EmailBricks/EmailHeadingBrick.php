<?php

namespace App\Mason\EmailBricks;

use Awcodes\Mason\Brick;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;

class EmailHeadingBrick extends Brick
{
    public static function getId(): string
    {
        return 'email-heading';
    }

    public static function getLabel(): string
    {
        return 'Nadpis';
    }

    public static function getIcon(): string|Heroicon|Htmlable|null
    {
        return Heroicon::OutlinedH1;
    }

    public static function toHtml(array $config, ?array $data = null): ?string
    {
        return view('mason.email-bricks.email-heading.index', $config)->render();
    }

    public static function configureBrickAction(Action $action): Action
    {
        return $action
            ->schema([
                TextInput::make('text')
                    ->label('Text nadpisu')
                    ->required(),
                Select::make('level')
                    ->label('Úroveň')
                    ->options([
                        'h1' => 'H1',
                        'h2' => 'H2',
                        'h3' => 'H3',
                    ])
                    ->default('h2')
                    ->required(),
                Select::make('alignment')
                    ->label('Zarovnanie')
                    ->options([
                        'left' => 'Vľavo',
                        'center' => 'Na stred',
                        'right' => 'Vpravo',
                    ])
                    ->default('left'),
            ]);
    }
}
