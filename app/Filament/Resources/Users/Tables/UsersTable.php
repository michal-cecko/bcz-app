<?php

namespace App\Filament\Resources\Users\Tables;

use App\Enums\RoleEnum;
use App\Models\User;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use STS\FilamentImpersonate\Actions\Impersonate;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Meno')
                    ->searchable(['first_name', 'last_name'])
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
                SelectFilter::make('global_role')
                    ->label('Globálna rola')
                    ->options(collect(RoleEnum::globalCases())
                        ->reject(fn (RoleEnum $r) => $r === RoleEnum::SUPER_ADMIN)
                        ->mapWithKeys(fn (RoleEnum $r) => [$r->value => $r->getLabel()])
                        ->all())
                    ->query(fn (Builder $query, array $data): Builder => $data['value']
                        ? $query->whereHas('roles', fn (Builder $q) => $q->where('name', $data['value']))
                        : $query),
                SelectFilter::make('team_role')
                    ->label('Tímová rola')
                    ->options(collect(RoleEnum::teamScopedCases())
                        ->mapWithKeys(fn (RoleEnum $r) => [$r->value => $r->getLabel()])
                        ->all())
                    ->query(function (Builder $query, array $data): Builder {
                        if (! $data['value']) {
                            return $query;
                        }

                        $tenant = filament()->getTenant();

                        return $tenant
                            ? $query->whereHas('teams', fn (Builder $q) => $q
                                ->where('teams.id', $tenant->id)
                                ->where('team_user.role', $data['value']))
                            : $query;
                    }),
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
