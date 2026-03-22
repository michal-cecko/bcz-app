<?php

namespace App\Filament\Pages\ProfileSchemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;

class CoachProfileSchema
{
    /**
     * @return list<Component>
     */
    public static function getFields(string $statePath): array
    {
        return [
            DatePicker::make("{$statePath}.date_started_coaching")
                ->label('Zaciatok trenerskej kariery')
                ->native(false)
                ->maxDate(now()),

            Tabs::make('coach_biography_tabs')
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

            Grid::make(2)
                ->schema([
                    SpatieMediaLibraryFileUpload::make("{$statePath}.main_background_image")
                        ->collection('main_background_image')
                        ->label('Hlavny obrazok (pozadie)')
                        ->disk('public')
                        ->visibility('public')
                        ->image(),
                    SpatieMediaLibraryFileUpload::make("{$statePath}.biography_image")
                        ->collection('biography_image')
                        ->label('Obrazok k biografii')
                        ->disk('public')
                        ->visibility('public')
                        ->image(),
                ]),
        ];
    }
}
