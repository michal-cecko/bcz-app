<?php

namespace App\Filament\Resources\Users\Pages;

use App\Enums\RoleEnum;
use App\Filament\Resources\Users\UserResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListUsers extends ListRecords
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }

    protected function getTableQuery(): ?Builder
    {
        $query = parent::getTableQuery();
        $user = auth()->user();

        if (! $user) {
            return $query;
        }

        // ATHLETE: only see other athletes
        if ($user->hasRole(RoleEnum::ATHLETE) && ! $user->hasRole([RoleEnum::SUPER_ADMIN, RoleEnum::ADMIN, RoleEnum::COACH, RoleEnum::EDITOR])) {
            $query->whereHas('roles', function (Builder $query): void {
                $query->where('name', RoleEnum::ATHLETE->value);
            });

            return $query;
        }

        // ADMIN: hide other ADMINs and SUPERADMINs (except self)
        if ($user->hasRole(RoleEnum::ADMIN) && ! $user->hasRole(RoleEnum::SUPER_ADMIN)) {
            $query->where(function (Builder $query) use ($user): void {
                $query->whereDoesntHave('roles', function (Builder $query): void {
                    $query->whereIn('name', [RoleEnum::ADMIN->value, RoleEnum::SUPER_ADMIN->value]);
                })->orWhere('id', $user->id);
            });
        }

        return $query;
    }
}
