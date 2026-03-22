<?php

namespace App\Filament\Pages\ProfileSchemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TagsInput;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;

class JudgeProfileSchema
{
    /**
     * @return list<Component>
     */
    public static function getFields(string $statePath): array
    {
        return [
            DatePicker::make("{$statePath}.date_started_judging")
                ->label('Zaciatok rozhodcovania')
                ->native(false)
                ->maxDate(now()),

            TagsInput::make("{$statePath}.disciplines")
                ->label('Discipliny')
                ->placeholder('Pridajte disciplinu')
                ->suggestions(['freestyle', 'speed', 'endurance', 'strength', 'parkour']),

            Tabs::make('judge_biography_tabs')
                ->tabs([
                    Tab::make('SK')
                        ->schema([
                            RichEditor::make("{$statePath}.biography.sk")
                                ->label('Biografia (SK)')
                                ->toolbarButtons(['bold', 'italic', 'link', 'orderedList', 'bulletList']),
                        ]),
                    Tab::make('EN')
                        ->schema([
                            RichEditor::make("{$statePath}.biography.en")
                                ->label('Biography (EN)')
                                ->toolbarButtons(['bold', 'italic', 'link', 'orderedList', 'bulletList']),
                        ]),
                    Tab::make('CS')
                        ->schema([
                            RichEditor::make("{$statePath}.biography.cs")
                                ->label('Biografie (CS)')
                                ->toolbarButtons(['bold', 'italic', 'link', 'orderedList', 'bulletList']),
                        ]),
                ]),

            SpatieMediaLibraryFileUpload::make("{$statePath}.hero_image")
                ->collection('hero_image')
                ->label('Hlavny obrazok')
                ->disk('public')
                ->visibility('public')
                ->image(),
        ];
    }
}
