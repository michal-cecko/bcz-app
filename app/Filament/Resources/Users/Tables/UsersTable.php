<?php

namespace App\Filament\Resources\Users\Tables;

use App\Enums\RoleEnum;
use App\Models\User;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use STS\FilamentImpersonate\Actions\Impersonate;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Meno')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('email')
                    ->label('E-mail')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('all_roles')
                    ->label('Roly')
                    ->badge()
                    ->state(function (User $record): array {
                        $globalRoles = $record->getRoleNames()
                            ->reject(fn ($r) => $r === 'panel_user')
                            ->values();

                        $tenant = filament()->getTenant();
                        $teamRoles = $tenant
                            ? $record->teams()
                                ->where('teams.id', $tenant->id)
                                ->pluck('team_user.role')
                                ->map(fn ($r) => $r instanceof RoleEnum ? $r->value : $r)
                            : collect();

                        return $globalRoles->merge($teamRoles)->unique()->values()->toArray();
                    })
                    ->formatStateUsing(fn (string $state): string => RoleEnum::tryFrom($state)?->getLabel() ?? $state),
                TextColumn::make('created_at')
                    ->label('Vytvorené')
                    ->since()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label('Aktualizované')
                    ->since()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                Impersonate::make(),
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
