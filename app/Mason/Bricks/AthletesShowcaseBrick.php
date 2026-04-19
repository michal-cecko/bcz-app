<?php

namespace App\Mason\Bricks;

use App\Mason\Support\TranslatableBrickFields;
use App\Models\User;
use Awcodes\Mason\Brick;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;
use Throwable;

class AthletesShowcaseBrick extends Brick
{
    public static function getId(): string
    {
        return 'athletes-showcase';
    }

    public static function getLabel(): string
    {
        return 'Atléti — výber';
    }

    public static function getIcon(): string|Heroicon|Htmlable|null
    {
        return Heroicon::OutlinedUsers;
    }

    /** @throws Throwable */
    public static function toHtml(array $config, ?array $data = null): ?string
    {
        $isRandom = (bool) ($config['random'] ?? true);
        $selectedIds = $config['athlete_ids'] ?? [];

        $query = User::query()
            ->whereNotNull('athlete_profile_approved_at')
            ->whereHas('athleteProfile')
            ->with(['athleteProfile', 'media']);

        if (! $isRandom && ! empty($selectedIds)) {
            $athletes = $query->whereIn('id', $selectedIds)->get();
        } else {
            $athletes = $query->inRandomOrder()->limit(5)->get();
        }

        return view('mason.bricks.athletes-showcase.index', array_merge($config, [
            'athletes' => $athletes,
        ]))->render();
    }

    public static function configureBrickAction(Action $action): Action
    {
        return $action
            ->slideOver()
            ->schema([
                TranslatableBrickFields::group(fn (string $locale) => [
                    TextInput::make("label.{$locale}")
                        ->label("Odznak ({$locale})"),
                    TextInput::make("title.{$locale}")
                        ->label("Nadpis ({$locale})"),
                    TextInput::make("description.{$locale}")
                        ->label("Popis ({$locale})"),
                ]),
                Toggle::make('random')
                    ->label('Náhodný výber atlétov')
                    ->default(true)
                    ->live(),
                Select::make('athlete_ids')
                    ->label('Vybrať atlétov')
                    ->multiple()
                    ->searchable()
                    ->preload()
                    ->options(fn () => User::query()
                        ->whereNotNull('athlete_profile_approved_at')
                        ->whereHas('athleteProfile')
                        ->pluck('name', 'id')
                        ->toArray()
                    )
                    ->visible(fn (Get $get): bool => ! $get('random')),
            ]);
    }
}
