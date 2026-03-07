<?php

namespace App\Filament\Resources\FaqCategories\RelationManagers;

use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class FaqsRelationManager extends RelationManager
{
    protected static string $relationship = 'faqs';

    protected static ?string $title = 'Otázky';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('Preklady')
                    ->tabs([
                        Tabs\Tab::make('SK')
                            ->schema([
                                TextInput::make('question.sk')
                                    ->label('Otázka (SK)')
                                    ->required(),
                                Textarea::make('answer.sk')
                                    ->label('Odpoveď (SK)')
                                    ->rows(4)
                                    ->required(),
                            ]),
                        Tabs\Tab::make('EN')
                            ->schema([
                                TextInput::make('question.en')
                                    ->label('Otázka (EN)'),
                                Textarea::make('answer.en')
                                    ->label('Odpoveď (EN)')
                                    ->rows(4),
                            ]),
                        Tabs\Tab::make('CZ')
                            ->schema([
                                TextInput::make('question.cz')
                                    ->label('Otázka (CZ)'),
                                Textarea::make('answer.cz')
                                    ->label('Odpoveď (CZ)')
                                    ->rows(4),
                            ]),
                    ])
                    ->columnSpanFull(),
                TextInput::make('sort_order')
                    ->label('Poradie')
                    ->numeric()
                    ->default(0),
                Toggle::make('is_published')
                    ->label('Publikované')
                    ->default(true),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->columns([
                TextColumn::make('question')
                    ->label('Otázka')
                    ->limit(60),
                IconColumn::make('is_published')
                    ->label('Publikované')
                    ->boolean(),
                TextColumn::make('sort_order')
                    ->label('Poradie')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->headerActions([
                CreateAction::make(),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }
}
