<?php

namespace App\Filament\Resources\SportCategories;

use App\Filament\Resources\SportCategories\Pages\CreateSportCategory;
use App\Filament\Resources\SportCategories\Pages\EditSportCategory;
use App\Filament\Resources\SportCategories\Pages\ListSportCategories;
use App\Filament\Resources\SportCategories\Schemas\SportCategoryForm;
use App\Filament\Resources\SportCategories\Tables\SportCategoriesTable;
use App\Models\SportCategory;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class SportCategoryResource extends Resource
{
    protected static ?string $model = SportCategory::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleGroup;

    protected static ?string $modelLabel = 'Športová kategória';

    protected static ?string $pluralModelLabel = 'Športové kategórie';

    protected static string|\UnitEnum|null $navigationGroup = 'Šport';

    protected static ?int $navigationSort = 1;

    protected static ?string $tenantOwnershipRelationshipName = 'team';

    public static function form(Schema $schema): Schema
    {
        return SportCategoryForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SportCategoriesTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSportCategories::route('/'),
            'create' => CreateSportCategory::route('/create'),
            'edit' => EditSportCategory::route('/{record}/edit'),
        ];
    }
}
