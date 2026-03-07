<?php

namespace App\Filament\Resources\Competitions;

use App\Filament\Resources\Competitions\Pages\CreateCompetition;
use App\Filament\Resources\Competitions\Pages\EditCompetition;
use App\Filament\Resources\Competitions\Pages\ListCompetitions;
use App\Filament\Resources\Competitions\RelationManagers\RegistrationsRelationManager;
use App\Filament\Resources\Competitions\RelationManagers\RoundsRelationManager;
use App\Filament\Resources\Competitions\RelationManagers\TimetableRelationManager;
use App\Filament\Resources\Competitions\Schemas\CompetitionForm;
use App\Filament\Resources\Competitions\Tables\CompetitionsTable;
use App\Models\Competition;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class CompetitionResource extends Resource
{
    protected static ?string $model = Competition::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedFire;

    protected static ?string $modelLabel = 'Súťaž';

    protected static ?string $pluralModelLabel = 'Súťaže';

    protected static string|\UnitEnum|null $navigationGroup = 'Súťaže';

    protected static ?int $navigationSort = 3;

    protected static bool $isScopedToTenant = false;

    public static function form(Schema $schema): Schema
    {
        return CompetitionForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CompetitionsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            RegistrationsRelationManager::class,
            RoundsRelationManager::class,
            TimetableRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCompetitions::route('/'),
            'create' => CreateCompetition::route('/create'),
            'edit' => EditCompetition::route('/{record}/edit'),
        ];
    }
}
