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

class FinishedCompetitionsBrick extends Brick
{
    public static function getId(): string
    {
        return 'finished-competitions';
    }

    public static function getLabel(): string
    {
        return 'Ukončené súťaže';
    }

    public static function getIcon(): string|Heroicon|Htmlable|null
    {
        return Heroicon::OutlinedClipboardDocumentCheck;
    }

    /** @throws Throwable */
    public static function toHtml(array $config, ?array $data = null): ?string
    {
        $competitions = Event::query()
            ->where('event_type', EventTypeEnum::Competition)
            ->where('is_published', true)
            ->where('date', '<', now())
            ->with(['eventCategory', 'team', 'media'])
            ->latest('date')
            ->limit(8)
            ->get();

        return view('mason.bricks.finished-competitions.index', array_merge($config, [
            'competitions' => $competitions,
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
