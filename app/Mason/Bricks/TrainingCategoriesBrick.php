<?php

namespace App\Mason\Bricks;

use App\Mason\Support\LinkPickerField;
use App\Mason\Support\TranslatableBrickFields;
use App\Models\Setting;
use App\Models\SportCategory;
use Awcodes\Mason\Brick;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;

class TrainingCategoriesBrick extends Brick
{
    public static function getId(): string
    {
        return 'training-categories';
    }

    public static function getLabel(): string
    {
        return __('bricks.names.training-categories');
    }

    public static function getIcon(): string|Heroicon|Htmlable|null
    {
        return Heroicon::OutlinedRectangleGroup;
    }

    public static function toHtml(array $config, ?array $data = null): ?string
    {
        $showAll = (bool) ($config['show_all'] ?? true);
        $categoryIds = $config['category_ids'] ?? [];

        $teamId = Setting::get('default_team_id');

        $query = SportCategory::query()
            ->where('is_active', true)
            ->where('team_id', $teamId)
            ->orderBy('sort_order');

        if (! $showAll && ! empty($categoryIds)) {
            $query->whereIn('id', $categoryIds);
        }

        $categories = $query->get();

        if (! $showAll && ! empty($categoryIds)) {
            $categories = $categories->sortBy(
                fn (SportCategory $cat) => array_search($cat->id, $categoryIds)
            )->values();
        }

        $config['categories'] = $categories;

        return view('mason.bricks.training-categories.index', $config)->render();
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
                    ->label(__('bricks.training_categories.show_all'))
                    ->helperText(__('bricks.training_categories.show_all_help'))
                    ->default(true)
                    ->live()
                    ->afterStateUpdated(fn (callable $set) => $set('category_ids', [])),
                Select::make('category_ids')
                    ->label(__('bricks.training_categories.categories'))
                    ->options(fn () => SportCategory::query()
                        ->where('is_active', true)
                        ->where('team_id', Setting::get('default_team_id'))
                        ->orderBy('sort_order')
                        ->get()
                        ->mapWithKeys(fn (SportCategory $cat) => [
                            $cat->id => $cat->getTranslation('name', app()->getLocale()),
                        ]))
                    ->multiple()
                    ->searchable()
                    ->disabled(fn (Get $get): bool => (bool) $get('show_all'))
                    ->helperText(__('bricks.training_categories.categories_help')),
            ]);
    }
}
