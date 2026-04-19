<?php

namespace App\Filament\Resources\Users\RelationManagers;

use App\Enums\MembershipStatusEnum;
use App\Enums\RoleEnum;
use App\Models\User;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class MembershipsRelationManager extends RelationManager
{
    protected static string $relationship = 'memberships';

    protected static ?string $title = 'Členstvá';

    protected static ?string $modelLabel = 'členstvo';

    protected static ?string $pluralModelLabel = 'Členstvá';

    public static function canViewForRecord(Model $ownerRecord, string $pageClass): bool
    {
        return $ownerRecord instanceof User && $ownerRecord->hasRole(RoleEnum::CUSTOMER->value);
    }

    public function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('team.name')
                    ->label('Tím')
                    ->formatStateUsing(fn ($record): string => $record->team?->getTranslation('name', 'sk') ?? '-')
                    ->searchable(),
                TextColumn::make('status')
                    ->label('Stav')
                    ->badge()
                    ->sortable(),
                TextColumn::make('season.name')
                    ->label('Sezóna')
                    ->placeholder('-')
                    ->sortable(),
                TextColumn::make('fee_amount')
                    ->label('Suma')
                    ->formatStateUsing(fn ($record): string => number_format((float) $record->fee_amount, 2).' '.$record->fee_currency)
                    ->sortable(),
                TextColumn::make('starts_at')
                    ->label('Začiatok')
                    ->date()
                    ->sortable(),
                TextColumn::make('ends_at')
                    ->label('Koniec')
                    ->date()
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('status')
                    ->label('Stav')
                    ->options(MembershipStatusEnum::translations()),
            ]);
    }
}
