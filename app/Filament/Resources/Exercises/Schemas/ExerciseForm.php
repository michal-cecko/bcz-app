<?php

namespace App\Filament\Resources\Exercises\Schemas;

use App\Enums\ComplexityLevelEnum;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Model;

class ExerciseForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('Preklady')
                    ->tabs([
                        Tabs\Tab::make('SK')
                            ->schema([
                                TextInput::make('name.sk')
                                    ->label('Názov (SK)')
                                    ->required(),
                                Textarea::make('description.sk')
                                    ->label('Popis (SK)')
                                    ->rows(3),
                            ]),
                        Tabs\Tab::make('EN')
                            ->schema([
                                TextInput::make('name.en')
                                    ->label('Názov (EN)'),
                                Textarea::make('description.en')
                                    ->label('Popis (EN)')
                                    ->rows(3),
                            ]),
                        Tabs\Tab::make('CZ')
                            ->schema([
                                TextInput::make('name.cs')
                                    ->label('Názov (CZ)'),
                                Textarea::make('description.cs')
                                    ->label('Popis (CZ)')
                                    ->rows(3),
                            ]),
                    ])
                    ->columnSpanFull(),
                Select::make('exercise_category_id')
                    ->label('Kategória')
                    ->relationship(name: 'exerciseCategory')
                    ->getOptionLabelFromRecordUsing(fn (Model $record): string => $record->getTranslation('name', 'sk'))
                    ->required()
                    ->preload()
                    ->searchable(['name->sk']),
                Select::make('complexity')
                    ->label('Obtiažnosť')
                    ->options(ComplexityLevelEnum::class)
                    ->required()
                    ->default(ComplexityLevelEnum::BASIC),
                SpatieMediaLibraryFileUpload::make('image')
                    ->collection('image')
                    ->disk('public')
                    ->visibility('public')
                    ->label('Obrázok'),
            ]);
    }
}
