<?php

namespace App\Filament\Pages\ProfileSchemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;

class AthleteProfileSchema
{
    /**
     * @return list<Component>
     */
    public static function getFields(string $statePath): array
    {
        return [
            DatePicker::make("{$statePath}.date_started_working_out")
                ->label('Zaciatok cvicenia')
                ->native(false)
                ->maxDate(now()),

            Tabs::make('athlete_journey_tabs')
                ->tabs([
                    Tab::make('SK')
                        ->schema([
                            RichEditor::make("{$statePath}.journey_text.sk")
                                ->label('Moj pribeh (SK)')
                                ->toolbarButtons(['bold', 'italic', 'link', 'orderedList', 'bulletList']),
                        ]),
                    Tab::make('EN')
                        ->schema([
                            RichEditor::make("{$statePath}.journey_text.en")
                                ->label('My story (EN)')
                                ->toolbarButtons(['bold', 'italic', 'link', 'orderedList', 'bulletList']),
                        ]),
                    Tab::make('CS')
                        ->schema([
                            RichEditor::make("{$statePath}.journey_text.cs")
                                ->label('Muj pribeh (CS)')
                                ->toolbarButtons(['bold', 'italic', 'link', 'orderedList', 'bulletList']),
                        ]),
                ]),

            Grid::make(2)
                ->schema([
                    SpatieMediaLibraryFileUpload::make("{$statePath}.journey_image")
                        ->collection('journey_image')
                        ->label('Obrazok k pribehu')
                        ->disk('public')
                        ->visibility('public')
                        ->image(),
                    SpatieMediaLibraryFileUpload::make("{$statePath}.main_image")
                        ->collection('main_image')
                        ->label('Hlavny obrazok')
                        ->disk('public')
                        ->visibility('public')
                        ->image(),
                ]),
        ];
    }
}
