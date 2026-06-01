<?php

namespace App\Mason\Bricks;

use App\Mason\Support\TranslatableBrickFields;
use App\Models\Event;
use App\Models\EventCategory;
use Awcodes\Mason\Brick;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;

class EventsShowcaseBrick extends Brick
{
    public static function getId(): string
    {
        return 'events-showcase';
    }

    public static function getLabel(): string
    {
        return __('bricks.names.events-showcase');
    }

    public static function getIcon(): string|Heroicon|Htmlable|null
    {
        return Heroicon::OutlinedStar;
    }

    public static function toHtml(array $config, ?array $data = null): ?string
    {
        $mode = $config['mode'] ?? 'random';
        $count = (int) ($config['count'] ?? 3);

        $events = match ($mode) {
            'category' => Event::query()
                ->where('is_published', true)
                ->where('event_category_id', $config['event_category_id'] ?? null)
                ->with(['eventCategory', 'team', 'media'])
                ->inRandomOrder()
                ->limit($count)
                ->get(),

            'specific' => ! empty($config['event_ids'])
                ? Event::query()
                    ->whereIn('id', $config['event_ids'])
                    ->where('is_published', true)
                    ->with(['eventCategory', 'team', 'media'])
                    ->get()
                    ->sortBy(fn (Event $e) => array_search($e->id, $config['event_ids']))
                    ->values()
                : collect(),

            default => Event::query()
                ->where('is_published', true)
                ->with(['eventCategory', 'team', 'media'])
                ->inRandomOrder()
                ->limit($count)
                ->get(),
        };

        $config['events'] = $events;

        return view('mason.bricks.events-showcase.index', $config)->render();
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
                    TextInput::make("view_all_text.{$locale}")
                        ->label(__('bricks.events_showcase.view_all_text')),
                ]),
                TextInput::make('view_all_url')
                    ->label(__('bricks.events_showcase.view_all_url'))
                    ->placeholder('/eventy'),
                Select::make('mode')
                    ->label(__('bricks.events_showcase.mode'))
                    ->options([
                        'random' => __('bricks.events_showcase.mode_random'),
                        'category' => __('bricks.events_showcase.mode_category'),
                        'specific' => __('bricks.events_showcase.mode_specific'),
                    ])
                    ->default('random')
                    ->live()
                    ->afterStateUpdated(function (callable $set): void {
                        $set('event_category_id', null);
                        $set('event_ids', []);
                    }),
                Select::make('event_category_id')
                    ->label(__('bricks.events_showcase.category'))
                    ->options(fn () => EventCategory::query()
                        ->where('is_active', true)
                        ->orderBy('sort_order')
                        ->get()
                        ->mapWithKeys(fn (EventCategory $c) => [
                            $c->id => $c->getTranslation('title', app()->getLocale()),
                        ]))
                    ->searchable()
                    ->disabled(fn (Get $get): bool => $get('mode') !== 'category'),
                Select::make('event_ids')
                    ->label(__('bricks.events_showcase.events'))
                    ->options(fn () => Event::query()
                        ->where('is_published', true)
                        ->orderByDesc('date')
                        ->get()
                        ->mapWithKeys(fn (Event $e) => [
                            $e->id => $e->getTranslation('title', app()->getLocale()),
                        ]))
                    ->multiple()
                    ->searchable()
                    ->disabled(fn (Get $get): bool => $get('mode') !== 'specific'),
                TextInput::make('count')
                    ->label(__('bricks.events_showcase.count'))
                    ->numeric()
                    ->default(3)
                    ->minValue(1)
                    ->maxValue(12),
            ]);
    }
}
