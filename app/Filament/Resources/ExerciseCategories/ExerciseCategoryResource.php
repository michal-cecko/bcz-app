<?php

namespace App\Filament\Resources\ExerciseCategories;

use App\Filament\Resources\ExerciseCategories\Pages\CreateExerciseCategory;
use App\Filament\Resources\ExerciseCategories\Pages\EditExerciseCategory;
use App\Filament\Resources\ExerciseCategories\Pages\ListExerciseCategories;
use App\Filament\Resources\ExerciseCategories\Schemas\ExerciseCategoryForm;
use App\Filament\Resources\ExerciseCategories\Tables\ExerciseCategoriesTable;
use App\Models\ExerciseCategory;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ExerciseCategoryResource extends Resource
{
    protected static ?string $model = ExerciseCategory::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $modelLabel = 'Kategória cvikov';

    protected static ?string $pluralModelLabel = 'Kategórie cvikov';

    protected static string|\UnitEnum|null $navigationGroup = 'Šport';

    protected static ?int $navigationSort = 2;

    protected static ?string $tenantOwnershipRelationshipName = 'team';

    public static function form(Schema $schema): Schema
    {
        return ExerciseCategoryForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ExerciseCategoriesTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListExerciseCategories::route('/'),
            'create' => CreateExerciseCategory::route('/create'),
            'edit' => EditExerciseCategory::route('/{record}/edit'),
        ];
    }
}
