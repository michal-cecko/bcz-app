<?php

namespace App\Filament\Resources\Exercises;

use App\Filament\Clusters\Trainings\TrainingsCluster;
use App\Filament\Resources\Exercises\Pages\CreateExercise;
use App\Filament\Resources\Exercises\Pages\EditExercise;
use App\Filament\Resources\Exercises\Pages\ListExercises;
use App\Filament\Resources\Exercises\Schemas\ExerciseForm;
use App\Filament\Resources\Exercises\Tables\ExercisesTable;
use App\Models\Exercise;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class ExerciseResource extends Resource
{
    protected static ?string $model = Exercise::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBolt;

    protected static ?string $modelLabel = 'cvik';

    protected static ?string $pluralModelLabel = 'Cviky';

    protected static bool $hasTitleCaseModelLabel = false;

    protected static ?string $cluster = TrainingsCluster::class;

    protected static ?int $navigationSort = 3;

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
        return ExerciseForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ExercisesTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListExercises::route('/'),
            'create' => CreateExercise::route('/create'),
            'edit' => EditExercise::route('/{record}/edit'),
        ];
    }
}
