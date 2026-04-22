<?php

namespace App\Filament\Resources\Users\Pages;

use App\Enums\DraftStatusEnum;
use App\Enums\RoleEnum;
use App\Filament\Resources\Users\UserResource;
use App\Models\User;
use App\Notifications\WelcomeToApp;
use App\Services\ProfileDraftService;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Password;
use STS\FilamentImpersonate\Actions\Impersonate;

class ViewUser extends ViewRecord
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        /** @var User $user */
        $user = $this->record;

        /** @var User $authUser */
        $authUser = auth()->user();
        $canManageProfiles = $authUser->hasAnyAppRole([RoleEnum::SUPER_ADMIN, RoleEnum::ADMIN, RoleEnum::TEAM_ADMIN]);

        return [
            Impersonate::make()
                ->record($this->getRecord()),
            Action::make('approveProfiles')
                ->label('Schváliť profily')
                ->icon(Heroicon::OutlinedCheckCircle)
                ->color('success')
                ->requiresConfirmation()
                ->modalHeading('Schváliť verejné profily')
                ->modalDescription('Schváliť všetky čakajúce profily tohto používateľa?')
                ->action(function () use ($user): void {
                    $service = new ProfileDraftService;
                    $approved = [];

                    if ($user->coachProfile?->draft_status === DraftStatusEnum::Pending) {
                        $service->approveDraft($user->coachProfile, $user);
                        $approved[] = 'trénera';
                    }
                    if ($user->athleteProfile?->draft_status === DraftStatusEnum::Pending) {
                        $service->approveDraft($user->athleteProfile, $user);
                        $approved[] = 'športovca';
                    }
                    if ($user->judgeProfile?->draft_status === DraftStatusEnum::Pending) {
                        $service->approveDraft($user->judgeProfile, $user);
                        $approved[] = 'porotcu';
                    }

                    Notification::make()
                        ->success()
                        ->title('Profil '.implode(', ', $approved).' schválený.')
                        ->send();
                })
                ->visible(fn () => $canManageProfiles && (
                    $user->fresh()->coachProfile?->draft_status === DraftStatusEnum::Pending
                    || $user->fresh()->athleteProfile?->draft_status === DraftStatusEnum::Pending
                    || $user->fresh()->judgeProfile?->draft_status === DraftStatusEnum::Pending
                )),
            Action::make('rejectProfiles')
                ->label('Zamietnuť profily')
                ->icon(Heroicon::OutlinedXCircle)
                ->color('danger')
                ->schema([
                    Textarea::make('reason')
                        ->label('Dôvod zamietnutia')
                        ->required()
                        ->rows(3),
                ])
                ->action(function (array $data) use ($user): void {
                    $service = new ProfileDraftService;

                    if ($user->coachProfile?->draft_status === DraftStatusEnum::Pending) {
                        $service->rejectDraft($user->coachProfile, $data['reason']);
                    }
                    if ($user->athleteProfile?->draft_status === DraftStatusEnum::Pending) {
                        $service->rejectDraft($user->athleteProfile, $data['reason']);
                    }
                    if ($user->judgeProfile?->draft_status === DraftStatusEnum::Pending) {
                        $service->rejectDraft($user->judgeProfile, $data['reason']);
                    }

                    Notification::make()
                        ->warning()
                        ->title('Profily zamietnuté.')
                        ->send();
                })
                ->visible(fn () => $canManageProfiles && (
                    $user->fresh()->coachProfile?->draft_status === DraftStatusEnum::Pending
                    || $user->fresh()->athleteProfile?->draft_status === DraftStatusEnum::Pending
                    || $user->fresh()->judgeProfile?->draft_status === DraftStatusEnum::Pending
                )),
            Action::make('sendLoginLink')
                ->label('Prihlásenie do profilu: '.$user->name)
                ->icon(Heroicon::OutlinedEnvelope)
                ->color('primary')
                ->requiresConfirmation()
                ->modalHeading('Odoslať pozvánku s prihlasovacím odkazom')
                ->modalDescription(fn () => "Na {$user->email} bude odoslaný uvítací e-mail s prihlasovacím odkazom platným 7 dní.")
                ->modalSubmitActionLabel('Odoslať')
                ->action(function () use ($user): void {
                    $user->notify(new WelcomeToApp);

                    Notification::make()
                        ->success()
                        ->title('Pozvánka s prihlasovacím odkazom bola odoslaná.')
                        ->send();
                }),
            Action::make('sendPasswordReset')
                ->label('Obnovit heslo')
                ->icon(Heroicon::OutlinedKey)
                ->color('gray')
                ->requiresConfirmation()
                ->modalHeading('Odoslat reset hesla')
                ->modalDescription(fn () => "E-mail na obnovu hesla bude odoslany na {$this->record->email}.")
                ->modalSubmitActionLabel('Odoslat')
                ->action(function (): void {
                    $status = Password::sendResetLink(['email' => $this->record->email]);

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
            EditAction::make(),
        ];
    }
}
