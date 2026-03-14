<?php

namespace App\Mason\Bricks;

use App\Mason\Support\BrickRichEditor;
use App\Mason\Support\LinkPickerField;
use App\Mason\Support\TranslatableBrickFields;
use Awcodes\Mason\Brick;
use Filament\Actions\Action;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;

class FeatureCardsBrick extends Brick
{
    public static function getId(): string
    {
        return 'feature-cards';
    }

    public static function getLabel(): string
    {
        return __('bricks.names.feature-cards');
    }

    public static function getIcon(): string|Heroicon|Htmlable|null
    {
        return Heroicon::OutlinedSquares2x2;
    }

    public static function toHtml(array $config, ?array $data = null): ?string
    {
        return view('mason.bricks.feature-cards.index', $config)->render();
    }

    public static function configureBrickAction(Action $action): Action
    {
        return $action
            ->slideOver()
            ->schema([
                TranslatableBrickFields::group(fn (string $locale) => [
                    TextInput::make("label.{$locale}")
                        ->label(__('bricks.fields.label')),
                    TextInput::make("title.{$locale}")
                        ->label(__('bricks.fields.title')),
                ]),
                Repeater::make('cards')
                    ->label(__('bricks.feature_cards.cards'))
                    ->schema([
                        Select::make('icon')
                            ->label(__('bricks.fields.icon'))
                            ->options([
                                'heroicon-o-bolt' => 'Bolt',
                                'heroicon-o-fire' => 'Fire',
                                'heroicon-o-star' => 'Star',
                                'heroicon-o-heart' => 'Heart',
                                'heroicon-o-trophy' => 'Trophy',
                                'heroicon-o-academic-cap' => 'Academic Cap',
                                'heroicon-o-arrow-trending-up' => 'Trending Up',
                                'heroicon-o-chart-bar' => 'Chart Bar',
                                'heroicon-o-clock' => 'Clock',
                                'heroicon-o-cog-6-tooth' => 'Cog',
                                'heroicon-o-cube' => 'Cube',
                                'heroicon-o-globe-alt' => 'Globe',
                                'heroicon-o-hand-raised' => 'Hand Raised',
                                'heroicon-o-light-bulb' => 'Light Bulb',
                                'heroicon-o-map-pin' => 'Map Pin',
                                'heroicon-o-megaphone' => 'Megaphone',
                                'heroicon-o-musical-note' => 'Musical Note',
                                'heroicon-o-puzzle-piece' => 'Puzzle',
                                'heroicon-o-rocket-launch' => 'Rocket',
                                'heroicon-o-shield-check' => 'Shield Check',
                                'heroicon-o-sparkles' => 'Sparkles',
                                'heroicon-o-user-group' => 'User Group',
                                'heroicon-o-wrench' => 'Wrench',
                                'heroicon-o-check-badge' => 'Check Badge',
                                'heroicon-o-flag' => 'Flag',
                            ])
                            ->searchable(),
                        LinkPickerField::make('card_', label: __('bricks.fields.link')),
                        TranslatableBrickFields::group(fn (string $locale) => [
                            TextInput::make("title.{$locale}")
                                ->label(__('bricks.fields.title'))
                                ->required(),
                            BrickRichEditor::make("description.{$locale}")
                                ->label(__('bricks.fields.description')),
                        ]),
                    ])
                    ->reorderable()
                    ->reorderableWithButtons()
                    ->cloneable()
                    ->collapsible(),
            ]);
    }
}
