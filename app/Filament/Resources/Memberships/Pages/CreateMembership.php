<?php

namespace App\Filament\Resources\Memberships\Pages;

use App\Filament\Resources\Memberships\MembershipResource;
use App\Models\Membership;
use App\Services\SeasonService;
use Filament\Facades\Filament;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateMembership extends CreateRecord
{
    protected static string $resource = MembershipResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        return app(SeasonService::class)->saveMembership(new Membership, $data, $this->form->getStatePath().'.status');
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['team_id'] = Filament::getTenant()->getKey();

        return $data;
    }
}
