<?php

namespace App\Mason\Bricks;

use App\Enums\EventTypeEnum;
use App\Models\Event;
use Awcodes\Mason\Brick;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;

class CompetitionTimetableBrick extends Brick
{
    public static function getId(): string
    {
        return 'competition-timetable';
    }

    public static function getLabel(): string
    {
        return __('bricks.names.competition-timetable');
    }

    public static function getIcon(): string|Heroicon|Htmlable|null
    {
        return Heroicon::OutlinedClock;
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
                'competitionDetail.timetableEntries',
            ])
            ->first();

        if (! $event?->competitionDetail || $event->competitionDetail->timetableEntries->isEmpty()) {
            return null;
        }

        return view('mason.bricks.competition-timetable.index', [
            'event' => $event,
            'entries' => $event->competitionDetail->timetableEntries,
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
