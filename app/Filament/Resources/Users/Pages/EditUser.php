<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Filament\Support\Enums\Alignment;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Password;
use STS\FilamentImpersonate\Actions\Impersonate;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    public static string|Alignment $formActionsAlignment = Alignment::End;

    protected function getHeaderActions(): array
    {
        /** @var User $user */
        $user = $this->record;

        return [
            Impersonate::make()
                ->record($this->getRecord()),
            Action::make('approveProfile')
                ->label('Schvalit profil')
                ->icon(Heroicon::OutlinedCheckCircle)
                ->color('success')
                ->requiresConfirmation()
                ->modalHeading('Schvalit verejny profil')
                ->modalDescription('Naozaj chcete schvalit verejny profil tohto pouzivatela?')
                ->action(fn () => $user->update(['public_profile_approved_at' => now()]))
                ->visible(fn () => $user->has_public_profile && ! $user->public_profile_approved_at),
            Action::make('revokeProfile')
                ->label('Zrusit schvalenie')
                ->icon(Heroicon::OutlinedXCircle)
                ->color('danger')
                ->requiresConfirmation()
                ->modalHeading('Zrusit schvalenie profilu')
                ->modalDescription('Profil pouzivatela bude odstraneny z verejneho zoznamu.')
                ->action(fn () => $user->update(['public_profile_approved_at' => null]))
                ->visible(fn () => $user->has_public_profile && $user->public_profile_approved_at),
            Action::make('sendPasswordReset')
                ->label('Obnovit heslo')
                ->icon(Heroicon::OutlinedKey)
                ->color('gray')
                ->requiresConfirmation()
                ->modalHeading('Odoslat reset hesla')
                ->modalDescription(fn () => "E-mail na obnovu hesla bude odoslany na {$user->email}.")
                ->modalSubmitActionLabel('Odoslat')
                ->action(function () use ($user): void {
                    $status = Password::sendResetLink(['email' => $user->email]);

                    if ($status === Password::RESET_LINK_SENT) {
                        Notification::make()
                            ->success()
                            ->title('E-mail na obnovu hesla bol odoslany.')
                            ->send();
                    } else {
                        Notification::make()
                            ->danger()
                            ->title('Nepodarilo sa odoslat e-mail.')
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
