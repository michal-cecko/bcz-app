<?php

namespace App\Filament\Resources\ExerciseCategories\RelationManagers;

use App\Enums\ComplexityLevelEnum;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ExercisesRelationManager extends RelationManager
{
    protected static string $relationship = 'exercises';

    protected static ?string $title = 'Cviky';

    protected static ?string $modelLabel = 'cvik';

    protected static ?string $pluralModelLabel = 'Cviky';

    public function form(Schema $schema): Schema
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

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                SpatieMediaLibraryImageColumn::make('image')
                    ->collection('image')
                    ->label('Obrázok')
                    ->circular(),
                TextColumn::make('name')
                    ->label('Názov')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('complexity')
                    ->label('Obtiažnosť')
                    ->sortable(),
            ])
            ->headerActions([
                CreateAction::make()
                    ->modalHeading('Vytvoriť cvik'),
            ])
            ->actions([
                EditAction::make()
                    ->modalHeading('Upraviť cvik'),
                DeleteAction::make()
                    ->modalHeading('Odstrániť cvik'),
            ]);
    }
}
