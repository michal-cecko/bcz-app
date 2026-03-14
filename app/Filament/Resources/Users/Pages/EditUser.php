<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Filament\Support\Enums\Alignment;
use Filament\Support\Icons\Heroicon;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    public static string|Alignment $formActionsAlignment = Alignment::End;

    protected function getHeaderActions(): array
    {
        /** @var User $user */
        $user = $this->record;

        return [
            Action::make('approveProfile')
                ->label('Schváliť profil')
                ->icon(Heroicon::OutlinedCheckCircle)
                ->color('success')
                ->requiresConfirmation()
                ->modalHeading('Schváliť verejný profil')
                ->modalDescription('Naozaj chcete schváliť verejný profil tohto používateľa?')
                ->action(fn () => $user->update(['public_profile_approved_at' => now()]))
                ->visible(fn () => $user->has_public_profile && ! $user->public_profile_approved_at),
            Action::make('revokeProfile')
                ->label('Zrušiť schválenie')
                ->icon(Heroicon::OutlinedXCircle)
                ->color('danger')
                ->requiresConfirmation()
                ->modalHeading('Zrušiť schválenie profilu')
                ->modalDescription('Profil používateľa bude odstránený z verejného zoznamu.')
                ->action(fn () => $user->update(['public_profile_approved_at' => null]))
                ->visible(fn () => $user->has_public_profile && $user->public_profile_approved_at),
            $this->getSaveFormAction()
                ->formId('form')
                ->icon(Heroicon::OutlinedPencilSquare),
            DeleteAction::make()
                ->icon(Heroicon::OutlinedTrash),
        ];
    }

    protected function getSaveFormAction(): Action
    {
        return parent::getSaveFormAction()
            ->icon(Heroicon::OutlinedPencilSquare);
    }
}
