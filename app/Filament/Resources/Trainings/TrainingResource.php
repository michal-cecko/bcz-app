<?php

namespace App\Filament\Resources\Trainings;

use App\Filament\Clusters\Trainings\TrainingsCluster;
use App\Filament\Resources\Trainings\Pages\CreateTraining;
use App\Filament\Resources\Trainings\Pages\EditTraining;
use App\Filament\Resources\Trainings\Pages\ListTrainings;
use App\Filament\Resources\Trainings\Pages\ViewTraining;
use App\Filament\Resources\Trainings\RelationManagers\CoachesRelationManager;
use App\Filament\Resources\Trainings\RelationManagers\RegistrationsRelationManager;
use App\Filament\Resources\Trainings\Schemas\TrainingForm;
use App\Filament\Resources\Trainings\Schemas\TrainingInfolist;
use App\Filament\Resources\Trainings\Tables\TrainingsTable;
use App\Models\Training;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class TrainingResource extends Resource
{
    protected static ?string $model = Training::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedAcademicCap;

    protected static ?string $modelLabel = 'tréning';

    protected static ?string $pluralModelLabel = 'Tréningy';

    protected static bool $hasTitleCaseModelLabel = false;

    protected static ?string $cluster = TrainingsCluster::class;

    protected static ?int $navigationSort = 1;

    protected static ?string $tenantOwnershipRelationshipName = 'team';

    public static function getGloballySearchableAttributes(): array
    {
        return ['title'];
    }

    public static function getGlobalSearchResultTitle(Model $record): string
    {
        return $record->getTranslation('title', 'sk');
    }

    public static function form(Schema $schema): Schema
    {
        return TrainingForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return TrainingInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TrainingsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            CoachesRelationManager::class,
            RegistrationsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListTrainings::route('/'),
            'create' => CreateTraining::route('/create'),
            'view' => ViewTraining::route('/{record}'),
            'edit' => EditTraining::route('/{record}/edit'),
        ];
    }
}
