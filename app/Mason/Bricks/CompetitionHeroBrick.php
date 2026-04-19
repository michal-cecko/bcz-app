<?php

namespace App\Mason\Bricks;

use App\Enums\EventTypeEnum;
use App\Mason\Support\LinkPickerField;
use App\Mason\Support\TranslatableBrickFields;
use App\Models\Event;
use Awcodes\Mason\Brick;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;
use Throwable;

class CompetitionHeroBrick extends Brick
{
    public static function getId(): string
    {
        return 'competition-hero';
    }

    public static function getLabel(): string
    {
        return 'Hero so štatistikami';
    }

    public static function getIcon(): string|Heroicon|Htmlable|null
    {
        return Heroicon::OutlinedTrophy;
    }

    /** @throws Throwable */
    public static function toHtml(array $config, ?array $data = null): ?string
    {
        $totalCompetitions = Event::where('event_type', EventTypeEnum::Competition)
            ->where('is_published', true)
            ->count();

        $latestEvent = Event::where('event_type', EventTypeEnum::Competition)
            ->where('is_published', true)
            ->latest('date')
            ->first();

        $heroImage = $latestEvent?->getFirstMediaUrl('detail_image')
            ?: $latestEvent?->getFirstMediaUrl('card_image');

        return view('mason.bricks.competition-hero.index', array_merge($config, [
            'totalCompetitions' => $totalCompetitions,
            'heroImage' => ! empty($config['background_image'])
                ? brick_media_url($config['background_image'])
                : $heroImage,
        ]))->render();
    }

    public static function configureBrickAction(Action $action): Action
    {
        return $action
            ->slideOver()
            ->schema([
                FileUpload::make('background_image')
                    ->image()
                    ->disk('public')
                    ->directory('bricks')
                    ->visibility('public')
                    ->label('Obrázok pozadia'),
                TranslatableBrickFields::group(fn (string $locale) => [
                    TextInput::make("badge.{$locale}")
                        ->label("Odznak ({$locale})"),
                    TextInput::make("headline1.{$locale}")
                        ->label("Nadpis riadok 1 ({$locale})"),
                    TextInput::make("headline2.{$locale}")
                        ->label("Nadpis riadok 2 — farebný ({$locale})"),
                    TextInput::make("subtitle.{$locale}")
                        ->label("Popis ({$locale})"),
                    LinkPickerField::make('cta_', $locale, 'Hlavné CTA', 'cta_text', 'Text tlačidla'),
                    LinkPickerField::make('secondary_cta_', $locale, 'Sekundárne CTA', 'secondary_cta_text', 'Text tlačidla'),
                ]),
                Repeater::make('stats')
                    ->label('Štatistiky')
                    ->schema([
                        TextInput::make('number')
                            ->label('Číslo')
                            ->required(),
                        TranslatableBrickFields::group(fn (string $locale) => [
                            TextInput::make("label.{$locale}")
                                ->label("Popis ({$locale})")
                                ->required($locale === 'sk'),
                        ]),
                    ])
                    ->defaultItems(3)
                    ->reorderable()
                    ->reorderableWithButtons()
                    ->collapsible()
                    ->maxItems(5),
            ]);
    }
}
