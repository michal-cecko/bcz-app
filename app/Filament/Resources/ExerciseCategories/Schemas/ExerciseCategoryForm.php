<?php

namespace App\Filament\Resources\ExerciseCategories\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Model;

class ExerciseCategoryForm
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
                                TextInput::make('name.cz')
                                    ->label('Názov (CZ)'),
                                Textarea::make('description.cz')
                                    ->label('Popis (CZ)')
                                    ->rows(3),
                            ]),
                    ])
                    ->columnSpanFull(),
                Select::make('sportCategories')
                    ->label('Športové kategórie')
                    ->relationship(name: 'sportCategories')
                    ->getOptionLabelFromRecordUsing(fn (Model $record): string => $record->getTranslation('name', 'sk'))
                    ->multiple()
                    ->preload()
                    ->searchable(['name->sk']),
                TextInput::make('sort_order')
                    ->label('Poradie')
                    ->numeric()
                    ->default(0),
            ]);
    }
}
