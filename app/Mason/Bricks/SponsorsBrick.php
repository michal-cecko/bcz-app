<?php

namespace App\Mason\Bricks;

use App\Models\Sponsor;
use Awcodes\Mason\Brick;
use Filament\Actions\Action;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;

class SponsorsBrick extends Brick
{
    public static function getId(): string
    {
        return 'sponsors';
    }

    public static function getLabel(): string
    {
        return __('bricks.names.sponsors');
    }

    public static function getIcon(): string|Heroicon|Htmlable|null
    {
        return Heroicon::OutlinedBuildingOffice;
    }

    public static function toHtml(array $config, ?array $data = null): ?string
    {
        $sponsorIds = collect($config['sponsors'] ?? [])
            ->pluck('sponsor_id')
            ->filter();

        $sponsors = Sponsor::query()
            ->whereIn('id', $sponsorIds)
            ->where('is_visible', true)
            ->get()
            ->keyBy('id');

        // Preserve order from config
        $ordered = $sponsorIds
            ->map(fn (string $id) => $sponsors->get($id))
            ->filter();

        return view('mason.bricks.sponsors.index', [
            'sponsors' => $ordered,
        ])->render();
    }

    public static function configureBrickAction(Action $action): Action
    {
        return $action
            ->slideOver()
            ->schema([
                Repeater::make('sponsors')
                    ->label(__('bricks.sponsors.items'))
                    ->schema([
                        Select::make('sponsor_id')
                            ->label(__('bricks.sponsors.sponsor'))
                            ->options(
                                Sponsor::query()
                                    ->where('is_visible', true)
                                    ->orderBy('sort_order')
                                    ->pluck('name', 'id')
                            )
                            ->required()
                            ->searchable(),
                    ])
                    ->compact()
                    ->reorderable()
                    ->reorderableWithButtons()
                    ->cloneable()
                    ->collapsible()
                    ->defaultItems(0),
            ]);
    }
}
