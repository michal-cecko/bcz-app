<?php

namespace App\Filament\Resources\Teams\RelationManagers;

use App\Filament\Resources\TeamPayouts\Tables\TeamPayoutsTable;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class PayoutsRelationManager extends RelationManager
{
    protected static string $relationship = 'payouts';

    protected static ?string $title = 'Výplaty';

    protected static ?string $modelLabel = 'výplatu';

    public function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public function table(Table $table): Table
    {
        return TeamPayoutsTable::configure($table);
    }
}
