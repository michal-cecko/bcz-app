<?php

namespace App\Filament\Actions;

use App\Models\Member;
use App\Services\MemberProfileService;
use Filament\Actions\DeleteAction;
use Illuminate\Support\Facades\Gate;

class DeleteFamilyMemberAction extends DeleteAction
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->label(__('family.delete'));
        $this->authorize('delete');
        $this->modalHeading(__('family.delete'));
        $this->modalDescription(fn (Member $record): string => __('family.delete_confirm', ['name' => $record->name]));
        $this->failureNotificationTitle(__('family.delete_blocked'));
        $this->using(function (Member $record): bool {
            Gate::authorize('delete', $record);

            return app(MemberProfileService::class)->deleteMember($record);
        });
    }
}
