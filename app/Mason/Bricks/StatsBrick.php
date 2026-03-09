<?php

namespace App\Mason\Bricks;

use App\Mason\Support\TranslatableBrickFields;
use Awcodes\Mason\Brick;
use Filament\Actions\Action;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;

class StatsBrick extends Brick
{
    public static function getId(): string
    {
        return 'stats';
    }

    public static function getLabel(): string
    {
        return __('bricks.names.stats');
    }

    public static function getIcon(): string|Heroicon|Htmlable|null
    {
        return Heroicon::OutlinedChartBar;
    }

    public static function toHtml(array $config, ?array $data = null): ?string
    {
        return view('mason.bricks.stats.index', $config)->render();
    }

    public static function configureBrickAction(Action $action): Action
    {
        return $action
            ->slideOver()
            ->schema([
                Repeater::make('items')
                    ->label(__('bricks.stats.items'))
                    ->schema([
                        TextInput::make('number')
                            ->label(__('bricks.fields.number'))
                            ->required(),
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
                        TranslatableBrickFields::group(fn (string $locale) => [
                            TextInput::make("label.{$locale}")
                                ->label(__('bricks.fields.label'))
                                ->required(),
                        ]),
                    ])
                    ->reorderable()
                    ->reorderableWithButtons()
                    ->cloneable()
                    ->collapsible(),
            ]);
    }
}
