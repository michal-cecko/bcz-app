<?php

namespace App\Filament\Resources\Athletes;

use App\Filament\Clusters\Events\EventsCluster;
use App\Filament\Resources\Athletes\Pages\ListAthletes;
use App\Filament\Resources\Athletes\Tables\AthletesTable;
use App\Models\User;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class AthleteResource extends Resource
{
    protected static ?string $model = User::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUsers;

    protected static ?string $modelLabel = 'športovca';

    protected static ?string $pluralModelLabel = 'Športovci';

    protected static bool $hasTitleCaseModelLabel = false;

    protected static ?string $cluster = EventsCluster::class;

    protected static ?int $navigationSort = 5;

    protected static bool $isScopedToTenant = false;

    protected static ?string $slug = 'athletes';

    public static function shouldRegisterNavigation(): bool
    {
        return ! auth()->user()?->isMemberLevel();
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->whereHas('athleteProfile');
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['name', 'email'];
    }

    public static function table(Table $table): Table
    {
        return AthletesTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAthletes::route('/'),
        ];
    }
}
