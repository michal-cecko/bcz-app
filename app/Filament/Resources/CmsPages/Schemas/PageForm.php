<?php

namespace App\Filament\Resources\CmsPages\Schemas;

use App\Enums\PageStatusEnum;
use App\Mason\Bricks\AboutPreviewBrick;
use App\Mason\Bricks\ContactFormBrick;
use App\Mason\Bricks\CtaBrick;
use App\Mason\Bricks\DividerBrick;
use App\Mason\Bricks\DonationInfoBrick;
use App\Mason\Bricks\FaqBrick;
use App\Mason\Bricks\FeatureCardsBrick;
use App\Mason\Bricks\FounderSpotlightBrick;
use App\Mason\Bricks\GalleryBrick;
use App\Mason\Bricks\HeadingBrick;
use App\Mason\Bricks\HeroBrick;
use App\Mason\Bricks\ImageBrick;
use App\Mason\Bricks\ImageTextBrick;
use App\Mason\Bricks\NumberedStepsBrick;
use App\Mason\Bricks\PersonCardsBrick;
use App\Mason\Bricks\QuoteBrick;
use App\Mason\Bricks\RichTextBrick;
use App\Mason\Bricks\SkillCardsBrick;
use App\Mason\Bricks\SocialCtaBrick;
use App\Mason\Bricks\SponsorsBrick;
use App\Mason\Bricks\SportCategoriesBrick;
use App\Mason\Bricks\StatsBrick;
use App\Mason\Bricks\TableBrick;
use App\Mason\Bricks\TimelineBrick;
use App\Mason\Bricks\TrainersBrick;
use Awcodes\Mason\Mason;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Schema;
use RalphJSmit\Filament\MediaLibrary\Filament\Forms\Components\MediaPicker;

class PageForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make(3)
                    ->columnSpanFull()
                    ->schema([
                        Section::make('Obsah')
                            ->schema([
                                Tabs::make('Preklady')
                                    ->tabs([
                                        Tabs\Tab::make('SK')
                                            ->schema([
                                                TextInput::make('title.sk')
                                                    ->label('Názov (SK)')
                                                    ->required(),
                                            ]),
                                        Tabs\Tab::make('EN')
                                            ->schema([
                                                TextInput::make('title.en')
                                                    ->label('Názov (EN)'),
                                            ]),
                                        Tabs\Tab::make('CZ')
                                            ->schema([
                                                TextInput::make('title.cs')
                                                    ->label('Názov (CZ)'),
                                            ]),
                                    ])
                                    ->columnSpanFull(),
                                TextInput::make('slug')
                                    ->disabled(fn ($record): bool => (bool) $record?->is_system)
                                    ->dehydrated()
                                    ->unique(table: 'pages', column: 'slug', ignoreRecord: true),
                                Mason::make('content')
                                    ->label('Obsah')
                                    ->bricks(self::bricks())
                                    ->columnSpanFull(),
                            ])
                            ->columnSpan(2),

                        Grid::make(1)
                            ->schema([
                                Section::make('Nastavenia')
                                    ->schema([
                                        Select::make('status')
                                            ->label('Stav')
                                            ->options(PageStatusEnum::class)
                                            ->default(PageStatusEnum::Draft)
                                            ->required(),
                                        DateTimePicker::make('published_at')
                                            ->label('Dátum publikovania'),
                                        TextInput::make('sort_order')
                                            ->label('Poradie')
                                            ->numeric()
                                            ->default(0),
                                        Toggle::make('is_system')
                                            ->label('Systémová stránka')
                                            ->disabled()
                                            ->dehydrated(),
                                    ]),

                                Section::make('SEO')
                                    ->schema([
                                        Tabs::make('SEO preklady')
                                            ->tabs([
                                                Tabs\Tab::make('SK')
                                                    ->schema([
                                                        TextInput::make('meta_title.sk')
                                                            ->label('Meta titulok (SK)'),
                                                        Textarea::make('meta_description.sk')
                                                            ->label('Meta popis (SK)')
                                                            ->rows(2),
                                                    ]),
                                                Tabs\Tab::make('EN')
                                                    ->schema([
                                                        TextInput::make('meta_title.en')
                                                            ->label('Meta titulok (EN)'),
                                                        Textarea::make('meta_description.en')
                                                            ->label('Meta popis (EN)')
                                                            ->rows(2),
                                                    ]),
                                                Tabs\Tab::make('CZ')
                                                    ->schema([
                                                        TextInput::make('meta_title.cs')
                                                            ->label('Meta titulok (CZ)'),
                                                        Textarea::make('meta_description.cs')
                                                            ->label('Meta popis (CZ)')
                                                            ->rows(2),
                                                    ]),
                                            ])
                                            ->columnSpanFull(),
                                        MediaPicker::make('featured_image')
                                            ->label('Hlavný obrázok'),
                                    ]),
                            ])
                            ->columnSpan(1),
                    ]),
            ]);
    }

    /** @return list<class-string<\Awcodes\Mason\Brick>> */
    private static function bricks(): array
    {
        return [
            HeroBrick::class,
            RichTextBrick::class,
            ImageBrick::class,
            ImageTextBrick::class,
            FeatureCardsBrick::class,
            CtaBrick::class,
            GalleryBrick::class,
            DividerBrick::class,
            QuoteBrick::class,
            HeadingBrick::class,
            StatsBrick::class,
            TableBrick::class,
            FaqBrick::class,
            TimelineBrick::class,
            PersonCardsBrick::class,
            ContactFormBrick::class,
            NumberedStepsBrick::class,
            SkillCardsBrick::class,
            TrainersBrick::class,
            SportCategoriesBrick::class,
            AboutPreviewBrick::class,
            FounderSpotlightBrick::class,
            SocialCtaBrick::class,
            SponsorsBrick::class,
            DonationInfoBrick::class,
        ];
    }
}
