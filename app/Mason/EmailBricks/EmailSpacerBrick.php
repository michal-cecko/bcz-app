<?php

namespace App\Mason\EmailBricks;

use Awcodes\Mason\Brick;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;

class EmailSpacerBrick extends Brick
{
    public static function getId(): string
    {
        return 'email-spacer';
    }

    public static function getLabel(): string
    {
        return 'Medzera';
    }

    public static function getIcon(): string|Heroicon|Htmlable|null
    {
        return Heroicon::OutlinedArrowsUpDown;
    }

    public static function toHtml(array $config, ?array $data = null): ?string
    {
        return view('mason.email-bricks.email-spacer.index', $config)->render();
    }

    public static function configureBrickAction(Action $action): Action
    {
        return $action
            ->schema([
                Select::make('size')
                    ->label('Veľkosť')
                    ->options([
                        'small' => 'Malá (10px)',
                        'medium' => 'Stredná (20px)',
                        'large' => 'Veľká (40px)',
                    ])
                    ->default('medium')
                    ->required(),
            ]);
    }
}
