<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use App\Models\User;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Admin-created users never set their own password here —
        // generate a random one. They reset it via the magic-login flow.
        $data['password'] = Hash::make(Str::random(32));

        return $data;
    }

    protected function afterCreate(): void
    {
        /** @var User $user */
        $user = $this->record;
        $teamId = $this->data['team_id'] ?? null;
        $roleIds = $this->data['roles'] ?? [];

        UserResource::syncTeamScopedRoles($user, $roleIds, $teamId);
    }
}
