<?php

namespace App\Filament\Resources\Users\Pages;

use App\Enums\DraftStatusEnum;
use App\Filament\Resources\Users\UserResource;
use App\Models\User;
use App\Services\ProfileDraftService;
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
            Action::make('approveCoachProfile')
                ->label('Schvalit profil trenera')
                ->icon(Heroicon::OutlinedCheckCircle)
                ->color('success')
                ->requiresConfirmation()
                ->action(function () use ($user): void {
                    (new ProfileDraftService)->approveDraft($user->coachProfile, $user);
                    Notification::make()->success()->title('Profil trenera schvaleny.')->send();
                })
                ->visible(fn () => $user->coachProfile?->draft_status === DraftStatusEnum::Pending),
            Action::make('approveAthleteProfile')
                ->label('Schvalit profil sportovca')
                ->icon(Heroicon::OutlinedCheckCircle)
                ->color('success')
                ->requiresConfirmation()
                ->action(function () use ($user): void {
                    (new ProfileDraftService)->approveDraft($user->athleteProfile, $user);
                    Notification::make()->success()->title('Profil sportovca schvaleny.')->send();
                })
                ->visible(fn () => $user->athleteProfile?->draft_status === DraftStatusEnum::Pending),
            Action::make('approveJudgeProfile')
                ->label('Schvalit profil porotcu')
                ->icon(Heroicon::OutlinedCheckCircle)
                ->color('success')
                ->requiresConfirmation()
                ->action(function () use ($user): void {
                    (new ProfileDraftService)->approveDraft($user->judgeProfile, $user);
                    Notification::make()->success()->title('Profil porotcu schvaleny.')->send();
                })
                ->visible(fn () => $user->judgeProfile?->draft_status === DraftStatusEnum::Pending),
            Action::make('revokeCoachProfile')
                ->label('Zrusit profil trenera')
                ->icon(Heroicon::OutlinedXCircle)
                ->color('danger')
                ->requiresConfirmation()
                ->action(fn () => $user->update(['coach_profile_approved_at' => null]))
                ->visible(fn () => $user->coach_profile_approved_at !== null),
            Action::make('revokeAthleteProfile')
                ->label('Zrusit profil sportovca')
                ->icon(Heroicon::OutlinedXCircle)
                ->color('danger')
                ->requiresConfirmation()
                ->action(fn () => $user->update(['athlete_profile_approved_at' => null]))
                ->visible(fn () => $user->athlete_profile_approved_at !== null),
            Action::make('revokeJudgeProfile')
                ->label('Zrusit profil porotcu')
                ->icon(Heroicon::OutlinedXCircle)
                ->color('danger')
                ->requiresConfirmation()
                ->action(fn () => $user->update(['judge_profile_approved_at' => null]))
                ->visible(fn () => $user->judge_profile_approved_at !== null),
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
