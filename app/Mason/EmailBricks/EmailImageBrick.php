<?php

namespace App\Mason\EmailBricks;

use App\Mason\Support\LinkPickerField;
use Awcodes\Mason\Brick;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;

class EmailImageBrick extends Brick
{
    public static function getId(): string
    {
        return 'email-image';
    }

    public static function getLabel(): string
    {
        return 'Obrázok';
    }

    public static function getIcon(): string|Heroicon|Htmlable|null
    {
        return Heroicon::OutlinedPhoto;
    }

    public static function toHtml(array $config, ?array $data = null): ?string
    {
        return view('mason.email-bricks.email-image.index', $config)->render();
    }

    public static function configureBrickAction(Action $action): Action
    {
        return $action
            ->slideOver()
            ->schema([
                FileUpload::make('image')
                    ->image()
                    ->disk('public')
                    ->directory('email-images')
                    ->visibility('public')
                    ->label('Obrázok')
                    ->required(),
                TextInput::make('alt')
                    ->label('Alternatívny text'),
                LinkPickerField::make(
                    prefix: 'image_',
                    label: 'Odkaz (voliteľné)',
                ),
            ]);
    }
}
