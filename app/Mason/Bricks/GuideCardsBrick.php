<?php

namespace App\Mason\Bricks;

use App\Mason\Support\LinkPickerField;
use App\Mason\Support\TranslatableBrickFields;
use Awcodes\Mason\Brick;
use Filament\Actions\Action;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Support\Icons\Heroicon;
use Guava\IconPicker\Forms\Components\IconPicker;
use Illuminate\Contracts\Support\Htmlable;

class GuideCardsBrick extends Brick
{
    public static function getId(): string
    {
        return 'guide-cards';
    }

    public static function getLabel(): string
    {
        return __('bricks.names.guide-cards');
    }

    public static function getIcon(): string|Heroicon|Htmlable|null
    {
        return Heroicon::OutlinedBookOpen;
    }

    public static function toHtml(array $config, ?array $data = null): ?string
    {
        return view('mason.bricks.guide-cards.index', $config)->render();
    }

    public static function configureBrickAction(Action $action): Action
    {
        return $action
            ->slideOver()
            ->schema([
                TranslatableBrickFields::group(fn (string $locale) => [
                    TextInput::make("title.{$locale}")
                        ->label('Titulok'),
                    TextInput::make("subtitle.{$locale}")
                        ->label('Podtitulok'),
                ]),
                Repeater::make('cards')
                    ->schema([
                        Select::make('color')
                            ->label('Farba')
                            ->options([
                                '#3B82F6' => 'Modrá',
                                '#22C55E' => 'Zelená',
                                '#8B5CF6' => 'Fialová',
                                '#FF2D2D' => 'Červená',
                                '#F59E0B' => 'Žltá',
                                '#14b8a6' => 'Teal (tyrkysová)',
                            ])
                            ->required()
                            ->searchable(),
                        IconPicker::make('icon')
                            ->label('Ikona')
                            ->sets(['heroicons'])
                            ->columns(3),
                        TranslatableBrickFields::group(fn (string $locale) => [
                            TextInput::make("title.{$locale}")
                                ->label('Titulok')
                                ->required(),
                            TextInput::make("subtitle.{$locale}")
                                ->label('Podtitulok'),
                            TextInput::make("button_text.{$locale}")
                                ->label('Text tlačidla'),
                        ]),
                        LinkPickerField::make('button_', label: 'Odkaz tlačidla'),
                        Repeater::make('steps')
                            ->schema([
                                TranslatableBrickFields::group(fn (string $locale) => [
                                    TextInput::make("text.{$locale}")
                                        ->label('Text')
                                        ->required(),
                                ]),
                            ])
                            ->addActionLabel('Pridať krok')
                            ->reorderable()
                            ->collapsible(),
                    ])
                    ->addActionLabel('Pridať kartu')
                    ->reorderable()
                    ->collapsible(),
            ]);
    }
}
