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
                TranslatableBrickFields::group(fn (string $locale) => [
                    TextInput::make("badge.{$locale}")
                        ->label('Odznak (voliteľný)')
                        ->placeholder('TRANSPARENTNOSŤ'),
                ]),
                Select::make('badge_color')
                    ->label('Farba odznaku')
                    ->options([
                        '#22C55E' => 'Zelená',
                        '#FF2D2D' => 'Červená',
                        '#3B82F6' => 'Modrá',
                        '#8B5CF6' => 'Fialová',
                        '#F59E0B' => 'Žltá',
                        '#EC4899' => 'Ružová',
                    ])
                    ->default('#22C55E')
                    ->native(false),
                TranslatableBrickFields::group(fn (string $locale) => [
                    TextInput::make("title.{$locale}")
                        ->label('Nadpis sekcie'),
                    TextInput::make("description.{$locale}")
                        ->label('Popis sekcie'),
                ]),
                Select::make('background_color')
                    ->label('Farba pozadia')
                    ->options([
                        '#0D0D0D' => 'Tmavá',
                        '#0A0A0A' => 'Čierna',
                        '#111111' => 'Tmavošedá',
                        '#1f2937' => 'Šedá (Gray 800)',
                    ])
                    ->default('#0D0D0D')
                    ->native(false),
                Repeater::make('items')
                    ->label(__('bricks.stats.items'))
                    ->schema([
                        TextInput::make('number')
                            ->label(__('bricks.fields.number'))
                            ->required(),
                        Select::make('color')
                            ->label('Farba čísla')
                            ->options([
                                '#22C55E' => 'Zelená',
                                '#FF2D2D' => 'Červená',
                                '#3B82F6' => 'Modrá',
                                '#8B5CF6' => 'Fialová',
                                '#F59E0B' => 'Žltá',
                                '#FFFFFF' => 'Biela',
                            ])
                            ->native(false),
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
                                'heroicon-o-check-badge' => 'Check Badge',
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
