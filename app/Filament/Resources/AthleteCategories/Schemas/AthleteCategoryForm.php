<?php

namespace App\Filament\Resources\AthleteCategories\Schemas;

use App\Enums\GenderEnum;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Model;

class AthleteCategoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Základné údaje')
                    ->schema([
                        Tabs::make('Preklady')
                            ->tabs([
                                Tabs\Tab::make('SK')
                                    ->schema([
                                        TextInput::make('name.sk')
                                            ->label('Názov (SK)')
                                            ->required(),
                                        Textarea::make('description.sk')
                                            ->label('Popis (SK)')
                                            ->rows(2),
                                    ]),
                                Tabs\Tab::make('EN')
                                    ->schema([
                                        TextInput::make('name.en')
                                            ->label('Názov (EN)'),
                                        Textarea::make('description.en')
                                            ->label('Popis (EN)')
                                            ->rows(2),
                                    ]),
                                Tabs\Tab::make('CZ')
                                    ->schema([
                                        TextInput::make('name.cz')
                                            ->label('Názov (CZ)'),
                                        Textarea::make('description.cz')
                                            ->label('Popis (CZ)')
                                            ->rows(2),
                                    ]),
                            ])
                            ->columnSpanFull(),
                        Select::make('parent_id')
                            ->relationship(name: 'parent')
                            ->getOptionLabelFromRecordUsing(fn (Model $record): string => $record->getTranslation('name', 'sk'))
                            ->label('Nadradená kategória')
                            ->placeholder('Žiadna (najvyššia úroveň)')
                            ->preload()
                            ->searchable(['name->sk']),
                        TextInput::make('sort_order')
                            ->label('Poradie')
                            ->numeric()
                            ->default(0),
                    ]),

                Section::make('Obmedzenia')
                    ->description('Voliteľné váhové a vekové obmedzenia pre túto kategóriu')
                    ->schema([
                        TextInput::make('min_weight')
                            ->label('Min. váha')
                            ->numeric()
                            ->suffix('kg'),
                        TextInput::make('max_weight')
                            ->label('Max. váha')
                            ->numeric()
                            ->suffix('kg'),
                        TextInput::make('min_age')
                            ->label('Min. vek')
                            ->numeric()
                            ->suffix('rokov'),
                        TextInput::make('max_age')
                            ->label('Max. vek')
                            ->numeric()
                            ->suffix('rokov'),
                        Select::make('gender')
                            ->label('Pohlavie')
                            ->options(GenderEnum::class)
                            ->placeholder('Všetky')
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
            ]);
    }
}
