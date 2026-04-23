<?php

namespace App\Filament\Resources\Memberships;

use App\Filament\Clusters\Finances\FinancesCluster;
use App\Filament\Resources\Memberships\Pages\CreateMembership;
use App\Filament\Resources\Memberships\Pages\EditMembership;
use App\Filament\Resources\Memberships\Pages\ListMemberships;
use App\Filament\Resources\Memberships\Schemas\MembershipForm;
use App\Filament\Resources\Memberships\Tables\MembershipsTable;
use App\Models\Membership;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class MembershipResource extends Resource
{
    protected static ?string $model = Membership::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedIdentification;

    protected static ?string $modelLabel = 'členstvo';

    protected static ?string $pluralModelLabel = 'Členstvá';

    protected static bool $hasTitleCaseModelLabel = false;

    protected static ?string $cluster = FinancesCluster::class;

    protected static ?int $navigationSort = 2;

    protected static ?string $tenantOwnershipRelationshipName = 'team';

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        if (auth()->user()?->isMemberLevel()) {
            $query->where('user_id', auth()->id());
        }

        return $query;
    }

    public static function form(Schema $schema): Schema
    {
        return MembershipForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return MembershipsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListMemberships::route('/'),
            'create' => CreateMembership::route('/create'),
            'edit' => EditMembership::route('/{record}/edit'),
        ];
    }
}
