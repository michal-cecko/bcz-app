<?php

namespace App\Mason\Bricks;

use App\Mason\Support\BrickRichEditor;
use App\Mason\Support\TranslatableBrickFields;
use Awcodes\Mason\Brick;
use Filament\Actions\Action;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;

class AchievementCardsBrick extends Brick
{
    public static function getId(): string
    {
        return 'achievement-cards';
    }

    public static function getLabel(): string
    {
        return __('bricks.names.achievement-cards');
    }

    public static function getIcon(): string|Heroicon|Htmlable|null
    {
        return Heroicon::OutlinedTrophy;
    }

    public static function toHtml(array $config, ?array $data = null): ?string
    {
        return view('mason.bricks.achievement-cards.index', $config)->render();
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
                        ->label('Nadpis'),
                    TextInput::make("description.{$locale}")
                        ->label('Popis'),
                ]),
                Repeater::make('cards')
                    ->schema([
                        TextInput::make('year')
                            ->label('Rok')
                            ->required(),
                        Select::make('badge_type')
                            ->label('Typ umiestnenia')
                            ->options([
                                'gold' => 'Zlato / 1. miesto',
                                'silver' => 'Striebro / 2. miesto',
                                'bronze' => 'Bronz / 3. miesto',
                                'top10' => 'Top 10',
                            ])
                            ->native(false)
                            ->searchable(),
                        TranslatableBrickFields::group(fn (string $locale) => [
                            TextInput::make("title.{$locale}")
                                ->label('Nadpis')
                                ->required(),
                            BrickRichEditor::make("description.{$locale}")
                                ->label('Popis'),
                            TextInput::make("badge_text.{$locale}")
                                ->label('Text odznaku'),
                        ]),
                    ])
                    ->reorderable()
                    ->reorderableWithButtons()
                    ->cloneable()
                    ->collapsible(),
            ]);
    }
}
