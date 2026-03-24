<?php

namespace App\Filament\Resources\Teams\RelationManagers;

use App\Filament\Resources\Payments\Tables\PaymentsTable;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class PaymentsRelationManager extends RelationManager
{
    protected static string $relationship = 'payments';

    protected static ?string $title = 'Platby';

    protected static ?string $modelLabel = 'platbu';

    public function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public function table(Table $table): Table
    {
        return PaymentsTable::configure($table);
    }
}
