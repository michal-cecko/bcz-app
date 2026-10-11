<?php

namespace App\Filament\Resources\Memberships\Pages;

use App\Filament\Resources\Memberships\MembershipResource;
use App\Services\SeasonService;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditMembership extends EditRecord
{
    protected static string $resource = MembershipResource::class;

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        return app(SeasonService::class)->saveMembership($record, $data, $this->form->getStatePath().'.status');
    }

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
