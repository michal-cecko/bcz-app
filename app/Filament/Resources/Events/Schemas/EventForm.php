<?php

namespace App\Filament\Resources\Events\Schemas;

use App\Mason\Bricks\CtaBrick;
use App\Mason\Bricks\DividerBrick;
use App\Mason\Bricks\FeatureCardsBrick;
use App\Mason\Bricks\GalleryBrick;
use App\Mason\Bricks\HeadingBrick;
use App\Mason\Bricks\HeroBrick;
use App\Mason\Bricks\ImageBrick;
use App\Mason\Bricks\ImageTextBrick;
use App\Mason\Bricks\QuoteBrick;
use App\Mason\Bricks\RichTextBrick;
use App\Mason\Bricks\StatsBrick;
use App\Mason\Bricks\TableBrick;
use Awcodes\Mason\Mason;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Model;
use RalphJSmit\Filament\MediaLibrary\Filament\Forms\Components\MediaPicker;

class EventForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make(3)
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
                                                Textarea::make('card_description.sk')
                                                    ->label('Popis na karte (SK)')
                                                    ->rows(2),
                                            ]),
                                        Tabs\Tab::make('EN')
                                            ->schema([
                                                TextInput::make('title.en')
                                                    ->label('Názov (EN)'),
                                                Textarea::make('card_description.en')
                                                    ->label('Popis na karte (EN)')
                                                    ->rows(2),
                                            ]),
                                        Tabs\Tab::make('CZ')
                                            ->schema([
                                                TextInput::make('title.cs')
                                                    ->label('Názov (CZ)'),
                                                Textarea::make('card_description.cs')
                                                    ->label('Popis na karte (CZ)')
                                                    ->rows(2),
                                            ]),
                                    ])
                                    ->columnSpanFull(),
                                TextInput::make('slug')
                                    ->disabled()
                                    ->dehydrated(),
                                Tabs::make('Obsah preklady')
                                    ->tabs([
                                        Tabs\Tab::make('SK')
                                            ->schema([
                                                Mason::make('content.sk')
                                                    ->label('Obsah (SK)')
                                                    ->bricks(self::bricks())
                                                    ->columnSpanFull(),
                                            ]),
                                        Tabs\Tab::make('EN')
                                            ->schema([
                                                Mason::make('content.en')
                                                    ->label('Obsah (EN)')
                                                    ->bricks(self::bricks())
                                                    ->columnSpanFull(),
                                            ]),
                                        Tabs\Tab::make('CZ')
                                            ->schema([
                                                Mason::make('content.cs')
                                                    ->label('Obsah (CZ)')
                                                    ->bricks(self::bricks())
                                                    ->columnSpanFull(),
                                            ]),
                                    ])
                                    ->columnSpanFull(),
                            ])
                            ->columnSpan(2),

                        Grid::make(1)
                            ->schema([
                                Section::make('Publikovanie')
                                    ->schema([
                                        Toggle::make('is_published')
                                            ->label('Publikované')
                                            ->default(false),
                                        DateTimePicker::make('published_at')
                                            ->label('Dátum publikovania'),
                                        Select::make('event_category_id')
                                            ->label('Kategória')
                                            ->relationship(name: 'eventCategory')
                                            ->getOptionLabelFromRecordUsing(fn (Model $record): string => $record->getTranslation('title', 'sk'))
                                            ->required()
                                            ->preload()
                                            ->searchable(['title->sk']),
                                    ]),

                                Section::make('Detaily')
                                    ->schema([
                                        DatePicker::make('date')
                                            ->label('Dátum')
                                            ->required(),
                                        DatePicker::make('date_end')
                                            ->label('Dátum konca'),
                                        TextInput::make('country')
                                            ->label('Krajina'),
                                        TextInput::make('city')
                                            ->label('Mesto'),
                                        TextInput::make('attendee_count')
                                            ->label('Počet účastníkov')
                                            ->numeric(),
                                        TextInput::make('client')
                                            ->label('Klient'),
                                        MediaPicker::make('card_image')
                                            ->label('Obrázok na karte'),
                                        MediaPicker::make('detail_image')
                                            ->label('Obrázok detailu'),
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
        ];
    }
}
