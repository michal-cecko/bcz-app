<?php

namespace App\Mason\Bricks;

use App\Mason\Support\BrickRichEditor;
use App\Mason\Support\TranslatableBrickFields;
use Awcodes\Mason\Brick;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;

class ProfileBioBrick extends Brick
{
    public static function getId(): string
    {
        return 'profile-bio';
    }

    public static function getLabel(): string
    {
        return __('bricks.names.profile-bio');
    }

    public static function getIcon(): string|Heroicon|Htmlable|null
    {
        return Heroicon::OutlinedBookOpen;
    }

    public static function toHtml(array $config, ?array $data = null): ?string
    {
        return view('mason.bricks.profile-bio.index', $config)->render();
    }

    public static function configureBrickAction(Action $action): Action
    {
        return $action
            ->slideOver()
            ->schema([
                FileUpload::make('image')
                    ->image()
                    ->disk('public')
                    ->directory('bricks')
                    ->visibility('public')
                    ->label('Obrázok'),
                TranslatableBrickFields::group(fn (string $locale) => [
                    TextInput::make("label.{$locale}")
                        ->label('Označenie'),
                    TextInput::make("title.{$locale}")
                        ->label('Nadpis')
                        ->required(),
                    BrickRichEditor::make("text.{$locale}")
                        ->label('Text')
                        ->required(),
                    TextInput::make("alt.{$locale}")
                        ->label('Alt text obrázka'),
                ]),
            ]);
    }
}
