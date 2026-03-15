<?php

namespace App\Mason\Bricks;

use App\Enums\EventTypeEnum;
use App\Models\Event;
use Awcodes\Mason\Brick;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;

class CompetitionBracketsBrick extends Brick
{
    public static function getId(): string
    {
        return 'competition-brackets';
    }

    public static function getLabel(): string
    {
        return __('bricks.names.competition-brackets');
    }

    public static function getIcon(): string|Heroicon|Htmlable|null
    {
        return Heroicon::OutlinedBolt;
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
                'competitionDetail.rounds.battles' => fn ($q) => $q->orderBy('bracket_position'),
                'competitionDetail.athleteCategories',
            ])
            ->first();

        if (! $event?->competitionDetail) {
            return null;
        }

        $roundsWithBattles = $event->competitionDetail->rounds
            ->filter(fn ($round) => $round->battles->isNotEmpty());

        if ($roundsWithBattles->isEmpty()) {
            return null;
        }

        return view('mason.bricks.competition-brackets.index', [
            'event' => $event,
            'detail' => $event->competitionDetail,
            'roundsWithBattles' => $roundsWithBattles,
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
