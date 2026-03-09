<?php

namespace App\Mason\Bricks;

use App\Mason\Support\LinkPickerField;
use App\Mason\Support\TranslatableBrickFields;
use App\Models\Faq;
use App\Models\FaqCategory;
use Awcodes\Mason\Brick;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;

class FaqBrick extends Brick
{
    public static function getId(): string
    {
        return 'faq';
    }

    public static function getLabel(): string
    {
        return __('bricks.names.faq');
    }

    public static function getIcon(): string|Heroicon|Htmlable|null
    {
        return Heroicon::OutlinedQuestionMarkCircle;
    }

    public static function toHtml(array $config, ?array $data = null): ?string
    {
        $query = Faq::query()
            ->where('is_published', true)
            ->with('faqCategory')
            ->orderBy('sort_order');

        $categoryIds = $config['category_ids'] ?? [];
        if (! empty($categoryIds)) {
            $query->whereIn('faq_category_id', $categoryIds);
        }

        $limit = $config['limit'] ?? null;
        if ($limit) {
            $query->limit((int) $limit);
        }

        $faqs = $query->get();

        $config['faqs'] = $faqs;

        return view('mason.bricks.faq.index', $config)->render();
    }

    public static function configureBrickAction(Action $action): Action
    {
        return $action
            ->slideOver()
            ->schema([
                TranslatableBrickFields::group(fn (string $locale) => [
                    TextInput::make("heading.{$locale}")
                        ->label(__('bricks.fields.title')),
                    LinkPickerField::make('link_', $locale, null, 'link_text', __('bricks.faq.show_all_link')),
                ]),
                Select::make('category_ids')
                    ->label(__('bricks.faq.categories'))
                    ->options(fn () => FaqCategory::query()->pluck('title', 'id')->map(fn ($t) => $t[app()->getLocale()] ?? $t['sk'] ?? ''))
                    ->multiple()
                    ->searchable()
                    ->helperText(__('bricks.faq.categories_help')),
                TextInput::make('limit')
                    ->label(__('bricks.faq.limit'))
                    ->numeric()
                    ->minValue(1)
                    ->helperText(__('bricks.faq.limit_help')),
            ]);
    }
}
