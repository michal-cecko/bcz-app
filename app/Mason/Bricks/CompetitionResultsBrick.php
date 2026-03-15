<?php

namespace App\Mason\Bricks;

use App\Enums\EventTypeEnum;
use App\Models\Event;
use Awcodes\Mason\Brick;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;

class CompetitionResultsBrick extends Brick
{
    public static function getId(): string
    {
        return 'competition-results';
    }

    public static function getLabel(): string
    {
        return __('bricks.names.competition-results');
    }

    public static function getIcon(): string|Heroicon|Htmlable|null
    {
        return Heroicon::OutlinedChartBar;
    }

    public static function toHtml(array $config, ?array $data = null): ?string
    {
        $eventId = $config['event_id'] ?? null;

        if (! $eventId) {
            return null;
        }

        $event = Event::query()
            ->where('id', $eventId)
            ->where('event_type', EventTypeEnum::Competition)
            ->where('is_published', true)
            ->with([
                'competitionDetail.rounds' => fn ($q) => $q->orderBy('sort_order'),
                'competitionDetail.rounds.athleteCategory',
                'competitionDetail.rounds.parts' => fn ($q) => $q->orderBy('sort_order'),
                'competitionDetail.rounds.parts.results' => fn ($q) => $q->orderBy('place'),
                'competitionDetail.rounds.parts.results.user',
                'competitionDetail.athleteCategories',
            ])
            ->first();

        if (! $event?->competitionDetail) {
            return null;
        }

        return view('mason.bricks.competition-results.index', [
            'event' => $event,
            'detail' => $event->competitionDetail,
        ])->render();
    }

    public static function configureBrickAction(Action $action): Action
    {
        return $action
            ->slideOver()
            ->schema([
                Select::make('event_id')
                    ->label(__('bricks.competition.event'))
                    ->options(fn () => Event::query()
                        ->where('event_type', EventTypeEnum::Competition)
                        ->where('is_published', true)
                        ->orderByDesc('date')
                        ->get()
                        ->mapWithKeys(fn (Event $e) => [
                            $e->id => $e->getTranslation('title', app()->getLocale()),
                        ]))
                    ->searchable()
                    ->required(),
            ]);
    }
}
