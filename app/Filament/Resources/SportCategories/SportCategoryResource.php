<?php

namespace App\Filament\Resources\SportCategories;

use App\Filament\Clusters\Trainings\TrainingsCluster;
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
use Illuminate\Database\Eloquent\Model;

class SportCategoryResource extends Resource
{
    protected static ?string $model = SportCategory::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleGroup;

    protected static ?string $modelLabel = 'športovú kategóriu';

    protected static ?string $pluralModelLabel = 'Športové kategórie';

    protected static bool $hasTitleCaseModelLabel = false;

    protected static ?string $cluster = TrainingsCluster::class;

    protected static ?int $navigationSort = 4;

    protected static ?string $tenantOwnershipRelationshipName = 'team';

    public static function shouldRegisterNavigation(): bool
    {
        return ! auth()->user()?->isMemberLevel();
    }

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
