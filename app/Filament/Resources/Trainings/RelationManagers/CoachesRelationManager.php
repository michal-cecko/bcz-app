<?php

namespace App\Filament\Resources\Trainings\RelationManagers;

use App\Enums\CoachRoleEnum;
use Filament\Actions\AttachAction;
use Filament\Actions\DetachAction;
use Filament\Actions\DetachBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CoachesRelationManager extends RelationManager
{
    protected static string $relationship = 'coaches';

    protected static ?string $title = 'Tréneri';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('role')
                    ->label('Rola')
                    ->options(CoachRoleEnum::class)
                    ->required()
                    ->default(CoachRoleEnum::MAIN),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Meno')
                    ->searchable(),
                TextColumn::make('email')
                    ->label('E-mail'),
                TextColumn::make('pivot.role')
                    ->label('Rola')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'main' => 'primary',
                        'secondary' => 'gray',
                        default => 'gray',
                    }),
            ])
            ->headerActions([
                AttachAction::make()
                    ->preloadRecordSelect()
                    ->form(fn (AttachAction $action): array => [
                        $action->getRecordSelect(),
                        Select::make('role')
                            ->label('Rola')
                            ->options(CoachRoleEnum::class)
                            ->required()
                            ->default(CoachRoleEnum::MAIN),
                    ]),
            ])
            ->recordActions([
                EditAction::make(),
                DetachAction::make(),
            ])
            ->toolbarActions([
                DetachBulkAction::make(),
            ]);
    }
}
