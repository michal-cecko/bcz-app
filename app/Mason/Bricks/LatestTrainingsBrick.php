<?php

namespace App\Mason\Bricks;

use App\Mason\Support\LinkPickerField;
use App\Mason\Support\TranslatableBrickFields;
use App\Models\Setting;
use App\Models\Training;
use Awcodes\Mason\Brick;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;

class LatestTrainingsBrick extends Brick
{
    public static function getId(): string
    {
        return 'latest-trainings';
    }

    public static function getLabel(): string
    {
        return __('bricks.names.latest-trainings');
    }

    public static function getIcon(): string|Heroicon|Htmlable|null
    {
        return Heroicon::OutlinedCalendarDays;
    }

    public static function toHtml(array $config, ?array $data = null): ?string
    {
        $showAll = (bool) ($config['show_all'] ?? false);
        $trainingIds = $config['training_ids'] ?? [];

        $teamId = Setting::get('default_team_id');

        if ($showAll) {
            $trainings = Training::query()
                ->where('is_active', true)
                ->where('team_id', $teamId)
                ->with(['sportCategory', 'coaches', 'registrations', 'team'])
                ->orderBy('sort_order')
                ->get();
        } elseif (! empty($trainingIds)) {
            $trainings = Training::query()
                ->whereIn('id', $trainingIds)
                ->where('is_active', true)
                ->with(['sportCategory', 'coaches', 'registrations', 'team'])
                ->get()
                ->sortBy(fn (Training $t) => array_search($t->id, $trainingIds))
                ->values();
        } else {
            $trainings = collect();
        }

        $config['trainings'] = $trainings;

        return view('mason.bricks.latest-trainings.index', $config)->render();
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
                    TextInput::make("subtitle.{$locale}")
                        ->label(__('bricks.fields.subtitle')),
                    LinkPickerField::make('cta_', $locale, null, 'cta_text', __('bricks.fields.button_text')),
                ]),
                Toggle::make('show_all')
                    ->label(__('bricks.latest_trainings.show_all'))
                    ->helperText(__('bricks.latest_trainings.show_all_help'))
                    ->live()
                    ->afterStateUpdated(fn (callable $set) => $set('training_ids', [])),
                Select::make('training_ids')
                    ->label(__('bricks.latest_trainings.trainings'))
                    ->options(fn () => Training::query()
                        ->where('is_active', true)
                        ->where('team_id', Setting::get('default_team_id'))
                        ->orderBy('sort_order')
                        ->get()
                        ->mapWithKeys(fn (Training $t) => [
                            $t->id => $t->getTranslation('title', app()->getLocale()),
                        ]))
                    ->multiple()
                    ->searchable()
                    ->disabled(fn (Get $get): bool => (bool) $get('show_all'))
                    ->helperText(__('bricks.latest_trainings.trainings_help')),
            ]);
    }
}
