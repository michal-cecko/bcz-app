<?php

namespace App\Filament\Resources\Disciplines;

use App\Filament\Clusters\Events\EventsCluster;
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
use Illuminate\Database\Eloquent\Model;

class DisciplineResource extends Resource
{
    protected static ?string $model = Discipline::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedTrophy;

    protected static ?string $modelLabel = 'disciplínu';

    protected static ?string $pluralModelLabel = 'Disciplíny';

    protected static bool $hasTitleCaseModelLabel = false;

    protected static ?string $cluster = EventsCluster::class;

    protected static ?int $navigationSort = 2;

    protected static bool $isScopedToTenant = false;

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
