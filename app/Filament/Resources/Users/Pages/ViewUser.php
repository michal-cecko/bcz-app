<?php

namespace App\Filament\Resources\Users\Pages;

use App\Enums\RoleEnum;
use App\Filament\Resources\Users\UserResource;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Password;

class ViewUser extends ViewRecord
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('impersonate')
                ->label('Prihlasiť sa ako')
                ->icon(Heroicon::OutlinedEye)
                ->color('gray')
                ->requiresConfirmation()
                ->modalHeading('Prihlasiť sa ako používateľ')
                ->modalDescription(fn () => "Budete prihlasený ako {$this->record->name}. Pôvodné prihlásenie bude obnovené po ukončení.")
                ->modalSubmitActionLabel('Prihlasiť sa')
                ->action(function (): void {
                    \App\Services\ImpersonationService::start($this->record);
                    $this->redirect(filament()->getUrl());
                })
                ->visible(fn () => Auth::id() !== $this->record->id
                    && Auth::user()->hasRole([RoleEnum::SUPER_ADMIN, RoleEnum::ADMIN])
                    && ! \App\Services\ImpersonationService::isImpersonating()),
            Action::make('sendPasswordReset')
                ->label('Obnoviť heslo')
                ->icon(Heroicon::OutlinedKey)
                ->color('gray')
                ->requiresConfirmation()
                ->modalHeading('Odoslať reset hesla')
                ->modalDescription(fn () => "E-mail na obnovu hesla bude odoslaný na {$this->record->email}.")
                ->modalSubmitActionLabel('Odoslať')
                ->action(function (): void {
                    $status = Password::sendResetLink(['email' => $this->record->email]);

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
            EditAction::make(),
        ];
    }
}
