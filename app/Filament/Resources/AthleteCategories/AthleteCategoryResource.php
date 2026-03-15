<?php

namespace App\Filament\Resources\AthleteCategories;

use App\Filament\Clusters\Events\EventsCluster;
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
use Illuminate\Database\Eloquent\Model;

class AthleteCategoryResource extends Resource
{
    protected static ?string $model = AthleteCategory::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUserGroup;

    protected static ?string $modelLabel = 'kategóriu športovcov';

    protected static ?string $pluralModelLabel = 'Kategórie športovcov';

    protected static bool $hasTitleCaseModelLabel = false;

    protected static ?string $cluster = EventsCluster::class;

    protected static ?int $navigationSort = 3;

    protected static bool $isScopedToTenant = false;

    public static function getGloballySearchableAttributes(): array
    {
        return ['name'];
    }

    public static function getGlobalSearchResultTitle(Model $record): string
    {
        return $record->getTranslation('name', 'sk');
    }

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
