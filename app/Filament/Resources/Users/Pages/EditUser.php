<?php

namespace App\Filament\Resources\Users\Pages;

use App\Enums\RoleEnum;
use App\Filament\Resources\Users\UserResource;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Filament\Support\Enums\Alignment;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Password;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    public static string|Alignment $formActionsAlignment = Alignment::End;

    protected function getHeaderActions(): array
    {
        /** @var User $user */
        $user = $this->record;

        return [
            Action::make('impersonate')
                ->label('Prihlasiť sa ako')
                ->icon(Heroicon::OutlinedEye)
                ->color('gray')
                ->requiresConfirmation()
                ->modalHeading('Prihlasiť sa ako používateľ')
                ->modalDescription(fn () => "Budete prihlasený ako {$user->name}. Pôvodné prihlásenie bude obnovené po ukončení.")
                ->modalSubmitActionLabel('Prihlasiť sa')
                ->action(function () use ($user): void {
                    \App\Services\ImpersonationService::start($user);
                    $this->redirect(filament()->getUrl());
                })
                ->visible(fn () => Auth::id() !== $user->id
                    && Auth::user()->hasRole([RoleEnum::SUPER_ADMIN, RoleEnum::ADMIN])
                    && ! \App\Services\ImpersonationService::isImpersonating()),
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
            Action::make('sendPasswordReset')
                ->label('Obnoviť heslo')
                ->icon(Heroicon::OutlinedKey)
                ->color('gray')
                ->requiresConfirmation()
                ->modalHeading('Odoslať reset hesla')
                ->modalDescription(fn () => "E-mail na obnovu hesla bude odoslaný na {$user->email}.")
                ->modalSubmitActionLabel('Odoslať')
                ->action(function () use ($user): void {
                    $status = Password::sendResetLink(['email' => $user->email]);

                    if ($status === Password::RESET_LINK_SENT) {
                        Notification::make()
                            ->success()
                            ->title('E-mail na obnovu hesla bol odoslaný.')
                            ->send();
                    } else {
                        Notification::make()
                            ->danger()
                            ->title('Nepodarilo sa odoslať e-mail.')
                            ->body(__($status))
                            ->send();
                    }
                }),
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
