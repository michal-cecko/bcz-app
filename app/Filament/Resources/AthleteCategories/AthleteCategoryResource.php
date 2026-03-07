<?php

namespace App\Filament\Resources\AthleteCategories;

use App\Filament\Resources\AthleteCategories\Pages\CreateAthleteCategory;
use App\Filament\Resources\AthleteCategories\Pages\EditAthleteCategory;
use App\Filament\Resources\AthleteCategories\Pages\ListAthleteCategories;
use App\Filament\Resources\AthleteCategories\Schemas\AthleteCategoryForm;
use App\Filament\Resources\AthleteCategories\Tables\AthleteCategoriesTable;
use App\Models\AthleteCategory;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class AthleteCategoryResource extends Resource
{
    protected static ?string $model = AthleteCategory::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUserGroup;

    protected static ?string $modelLabel = 'Kategória športovcov';

    protected static ?string $pluralModelLabel = 'Kategórie športovcov';

    protected static string|\UnitEnum|null $navigationGroup = 'Súťaže';

    protected static ?int $navigationSort = 2;

    protected static bool $isScopedToTenant = false;

    public static function form(Schema $schema): Schema
    {
        return AthleteCategoryForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AthleteCategoriesTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAthleteCategories::route('/'),
            'create' => CreateAthleteCategory::route('/create'),
            'edit' => EditAthleteCategory::route('/{record}/edit'),
        ];
    }
}
