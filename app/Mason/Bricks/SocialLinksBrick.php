<?php

namespace App\Mason\Bricks;

use App\Mason\Support\TranslatableBrickFields;
use Awcodes\Mason\Brick;
use Filament\Actions\Action;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Fieldset;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;

class SocialLinksBrick extends Brick
{
    public static function getId(): string
    {
        return 'social-links';
    }

    public static function getLabel(): string
    {
        return __('bricks.names.social-links');
    }

    public static function getIcon(): string|Heroicon|Htmlable|null
    {
        return Heroicon::OutlinedGlobeAlt;
    }

    public static function toHtml(array $config, ?array $data = null): ?string
    {
        return view('mason.bricks.social-links.index', $config)->render();
    }

    public static function configureBrickAction(Action $action): Action
    {
        return $action
            ->slideOver()
            ->schema([
                TranslatableBrickFields::group(fn (string $locale) => [
                    TextInput::make("label.{$locale}")
                        ->label('Označenie'),
                    TextInput::make("title.{$locale}")
                        ->label('Nadpis')
                        ->required(),
                    TextInput::make("description.{$locale}")
                        ->label('Popis'),
                ]),
                Repeater::make('socials')
                    ->label('Sociálne siete')
                    ->schema([
                        TextInput::make('url')
                            ->label('URL')
                            ->required(),
                        Select::make('platform')
                            ->label('Platforma')
                            ->options([
                                'website' => 'Webstránka',
                                'instagram' => 'Instagram',
                                'youtube' => 'YouTube',
                                'tiktok' => 'TikTok',
                                'facebook' => 'Facebook',
                            ])
                            ->required()
                            ->native(false),
                        TranslatableBrickFields::group(fn (string $locale) => [
                            TextInput::make("name.{$locale}")
                                ->label('Názov'),
                            TextInput::make("handle.{$locale}")
                                ->label('Handle / URL text'),
                        ]),
                    ])
                    ->reorderable()
                    ->reorderableWithButtons()
                    ->cloneable()
                    ->collapsible(),
                Fieldset::make('Kontakt')
                    ->schema([
                        TranslatableBrickFields::group(fn (string $locale) => [
                            TextInput::make("email.{$locale}")
                                ->label('Email'),
                            TextInput::make("phone.{$locale}")
                                ->label('Telefón'),
                        ]),
                    ]),
            ]);
    }
}
