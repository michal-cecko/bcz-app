<?php

namespace App\Mason\Bricks;

use App\Mason\Support\LinkPickerField;
use App\Mason\Support\TranslatableBrickFields;
use App\Models\Faq;
use App\Models\FaqCategory;
use Awcodes\Mason\Brick;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Utilities\Get;
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
        $showAll = (bool) ($config['show_all'] ?? false);
        $faqIds = $config['faq_ids'] ?? [];

        if ($showAll) {
            $faqs = Faq::query()
                ->where('is_published', true)
                ->with('faqCategory')
                ->orderBy('sort_order')
                ->get();
        } elseif (! empty($faqIds)) {
            $faqs = Faq::query()
                ->whereIn('id', $faqIds)
                ->where('is_published', true)
                ->with('faqCategory')
                ->get()
                ->sortBy(fn (Faq $faq) => array_search($faq->id, $faqIds))
                ->values();
        } else {
            $faqs = collect();
        }

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
                Toggle::make('show_all')
                    ->label(__('bricks.faq.show_all'))
                    ->helperText(__('bricks.faq.show_all_help'))
                    ->live()
                    ->afterStateUpdated(fn (callable $set) => $set('faq_ids', [])),
                Select::make('faq_ids')
                    ->label(__('bricks.faq.questions'))
                    ->options(fn () => Faq::query()
                        ->where('is_published', true)
                        ->orderBy('sort_order')
                        ->get()
                        ->mapWithKeys(fn (Faq $faq) => [$faq->id => $faq->getTranslation('question', app()->getLocale())]))
                    ->multiple()
                    ->searchable()
                    ->disabled(fn (Get $get): bool => (bool) $get('show_all'))
                    ->helperText(__('bricks.faq.questions_help'))
                    ->createOptionForm([
                        Tabs::make('Preklady')
                            ->tabs([
                                Tabs\Tab::make('SK')
                                    ->schema([
                                        TextInput::make('question.sk')
                                            ->label('Otázka (SK)')
                                            ->required(),
                                        Textarea::make('answer.sk')
                                            ->label('Odpoveď (SK)')
                                            ->rows(4)
                                            ->required(),
                                    ]),
                                Tabs\Tab::make('EN')
                                    ->schema([
                                        TextInput::make('question.en')
                                            ->label('Otázka (EN)'),
                                        Textarea::make('answer.en')
                                            ->label('Odpoveď (EN)')
                                            ->rows(4),
                                    ]),
                                Tabs\Tab::make('CZ')
                                    ->schema([
                                        TextInput::make('question.cs')
                                            ->label('Otázka (CZ)'),
                                        Textarea::make('answer.cs')
                                            ->label('Odpoveď (CZ)')
                                            ->rows(4),
                                    ]),
                            ])
                            ->columnSpanFull(),
                        Select::make('faq_category_id')
                            ->label('Kategória')
                            ->options(fn () => FaqCategory::query()
                                ->get()
                                ->mapWithKeys(fn ($c) => [$c->id => $c->getTranslation('title', app()->getLocale())]))
                            ->searchable(),
                        Toggle::make('is_published')
                            ->label('Publikované')
                            ->default(true),
                    ])
                    ->createOptionUsing(function (array $data): string {
                        $faq = Faq::create([
                            'question' => $data['question'],
                            'answer' => $data['answer'],
                            'faq_category_id' => $data['faq_category_id'] ?? null,
                            'is_published' => $data['is_published'] ?? true,
                            'sort_order' => Faq::query()->max('sort_order') + 1,
                        ]);

                        return $faq->id;
                    }),
            ]);
    }
}
