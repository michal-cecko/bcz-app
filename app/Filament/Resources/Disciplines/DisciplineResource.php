<?php

namespace App\Filament\Resources\Disciplines;

use App\Filament\Resources\Disciplines\Pages\CreateDiscipline;
use App\Filament\Resources\Disciplines\Pages\EditDiscipline;
use App\Filament\Resources\Disciplines\Pages\ListDisciplines;
use App\Filament\Resources\Disciplines\Schemas\DisciplineForm;
use App\Filament\Resources\Disciplines\Tables\DisciplinesTable;
use App\Models\Discipline;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class DisciplineResource extends Resource
{
    protected static ?string $model = Discipline::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedTrophy;

    protected static ?string $modelLabel = 'Disciplína';

    protected static ?string $pluralModelLabel = 'Disciplíny';

    protected static string|\UnitEnum|null $navigationGroup = 'Súťaže';

    protected static ?int $navigationSort = 1;

    protected static bool $isScopedToTenant = false;

    public static function form(Schema $schema): Schema
    {
        return DisciplineForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return DisciplinesTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListDisciplines::route('/'),
            'create' => CreateDiscipline::route('/create'),
            'edit' => EditDiscipline::route('/{record}/edit'),
        ];
    }
}
