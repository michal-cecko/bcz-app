<?php

namespace App\Mason\Bricks;

use App\Enums\EventTypeEnum;
use App\Mason\Support\TranslatableBrickFields;
use App\Models\Event;
use Awcodes\Mason\Brick;
use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;
use Throwable;

class CompetitionsArchiveBrick extends Brick
{
    public static function getId(): string
    {
        return 'competitions-archive';
    }

    public static function getLabel(): string
    {
        return 'Archív súťaží';
    }

    public static function getIcon(): string|Heroicon|Htmlable|null
    {
        return Heroicon::OutlinedTrophy;
    }

    /** @throws Throwable */
    public static function toHtml(array $config, ?array $data = null): ?string
    {
        $upcoming = Event::query()
            ->where('event_type', EventTypeEnum::Competition)
            ->where('is_published', true)
            ->where('date', '>=', now())
            ->with(['eventCategory', 'team', 'competitionDetail.disciplines', 'media'])
            ->orderBy('date')
            ->get();

        return view('mason.bricks.competitions-archive.index', array_merge($config, [
            'upcoming' => $upcoming,
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
            ]);
    }
}
