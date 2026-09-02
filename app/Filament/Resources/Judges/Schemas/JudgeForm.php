<?php

namespace App\Filament\Resources\Judges\Schemas;

use App\Filament\Schemas\PublicProfileSchema;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;

class JudgeForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Základné údaje')
                ->schema([
                    TextInput::make('name')
                        ->label('Meno')
                        ->required()
                        ->maxLength(255)
                        ->live(onBlur: true),
                    TextInput::make('slug')
                        ->label('Slug')
                        ->helperText('Auto-generuje sa z mena.')
                        ->maxLength(255),
                    DatePicker::make('date_started_judging')
                        ->label('Začiatok porotcovania')
                        ->native(false)
                        ->maxDate(now()),
                    TagsInput::make('disciplines')
                        ->label('Disciplíny')
                        ->placeholder('Pridajte disciplínu')
                        ->suggestions(['freestyle', 'speed', 'endurance', 'strength', 'parkour']),
                    KeyValue::make('socials')
                        ->label('Sociálne siete')
                        ->keyLabel('Platforma')
                        ->valueLabel('URL')
                        ->columnSpanFull(),
                ])
                ->columns(2),

            Tabs::make('biography_tabs')
                ->tabs([
                    Tab::make('SK')->schema([
                        RichEditor::make('biography.sk')
                            ->label('Biografia (SK)')
                            ->toolbarButtons(['bold', 'italic', 'link', 'orderedList', 'bulletList']),
                    ]),
                    Tab::make('EN')->schema([
                        RichEditor::make('biography.en')
                            ->label('Biography (EN)')
                            ->toolbarButtons(['bold', 'italic', 'link', 'orderedList', 'bulletList']),
                    ]),
                    Tab::make('CS')->schema([
                        RichEditor::make('biography.cs')
                            ->label('Biografie (CS)')
                            ->toolbarButtons(['bold', 'italic', 'link', 'orderedList', 'bulletList']),
                    ]),
                ])
                ->columnSpanFull(),

            Section::make('Obrázky')
                ->schema([
                    SpatieMediaLibraryFileUpload::make('profile_image')
                        ->collection('profile_image')
                        ->label('Profilová fotografia')
                        ->disk('public')
                        ->visibility('public')
                        ->image()
                        ->maxSize(10240),
                    SpatieMediaLibraryFileUpload::make('hero_image')
                        ->collection('hero_image')
                        ->label('Hlavný obrázok')
                        ->disk('public')
                        ->visibility('public')
                        ->image()
                        ->maxSize(10240),
                    SpatieMediaLibraryFileUpload::make('gallery')
                        ->collection('gallery')
                        ->label('Galéria')
                        ->disk('public')
                        ->visibility('public')
                        ->multiple()
                        ->reorderable()
                        ->image()
                        ->maxSize(10240)
                        ->columnSpanFull(),
                ])
                ->columns(2),

            Section::make('Certifikáty')
                ->schema([
                    PublicProfileSchema::certificationsRepeater()
                        ->relationship()
                        ->columnSpanFull(),
                ]),
        ]);
    }
}
