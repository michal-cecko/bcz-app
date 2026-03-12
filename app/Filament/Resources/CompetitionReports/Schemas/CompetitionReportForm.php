<?php

namespace App\Filament\Resources\CompetitionReports\Schemas;

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
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Model;

class CompetitionReportForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Základné údaje')
                    ->schema([
                        Select::make('competition_id')
                            ->label('Súťaž')
                            ->relationship(name: 'competition')
                            ->getOptionLabelFromRecordUsing(fn (Model $record): string => $record->getTranslation('name', 'sk'))
                            ->required()
                            ->preload()
                            ->searchable(['name->sk']),
                        Select::make('user_id')
                            ->relationship('user', 'name')
                            ->label('Autor')
                            ->required()
                            ->preload()
                            ->searchable(),
                        Tabs::make('Preklady názvu')
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
                        Toggle::make('is_published')
                            ->label('Publikovaný')
                            ->default(false),
                        DateTimePicker::make('published_at')
                            ->label('Dátum publikovania'),
                    ]),

                Section::make('Obsah')
                    ->schema([
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
