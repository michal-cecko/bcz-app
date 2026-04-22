<?php

namespace App\Filament\Resources\Users\Pages;

use App\Enums\DraftStatusEnum;
use App\Enums\RoleEnum;
use App\Filament\Resources\Users\UserResource;
use App\Models\AthleteProfile;
use App\Models\CoachProfile;
use App\Models\JudgeProfile;
use App\Models\User;
use App\Services\ProfileDraftService;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Forms\Components\Textarea;
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

        /** @var User $authUser */
        $authUser = auth()->user();
        $canManageProfiles = $authUser->hasAnyAppRole([RoleEnum::SUPER_ADMIN, RoleEnum::ADMIN, RoleEnum::TEAM_ADMIN]);

        return [
            Impersonate::make()
                ->record($this->getRecord()),
            // Approve pending profiles (shown only when drafts exist)
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
            // Reject pending profiles
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

    /**
     * Non-admin users editing themselves: route profile data through draft workflow.
     * Admins: sync team-scoped roles to the team_user pivot based on the form's Team select.
     */
    protected function afterSave(): void
    {
        /** @var User $authUser */
        $authUser = auth()->user();

        /** @var User $record */
        $record = $this->record;

        // Admin path: move team-scoped roles from Spatie to the team_user pivot.
        $teamId = $this->data['team_id'] ?? null;
        $roleIds = $this->data['roles'] ?? [];
        UserResource::syncTeamScopedRoles($record, $roleIds, $teamId);

        // Only apply draft workflow when user edits themselves and is not admin
        if ($authUser->id !== $record->id) {
            return;
        }

        if ($authUser->hasAnyAppRole([RoleEnum::SUPER_ADMIN, RoleEnum::ADMIN, RoleEnum::TEAM_ADMIN])) {
            return;
        }

        $service = new ProfileDraftService;

        $this->submitProfileAsDraft($service, $record->coachProfile, $record);
        $this->submitProfileAsDraft($service, $record->athleteProfile, $record);
        $this->submitProfileAsDraft($service, $record->judgeProfile, $record);
    }

    protected function submitProfileAsDraft(
        ProfileDraftService $service,
        AthleteProfile|CoachProfile|JudgeProfile|null $profile,
        User $user,
    ): void {
        if (! $profile) {
            return;
        }

        $mainFields = match (true) {
            $profile instanceof CoachProfile => ['biography', 'date_started_coaching'],
            $profile instanceof AthleteProfile => ['journey_text', 'date_started_working_out'],
            $profile instanceof JudgeProfile => ['biography', 'disciplines', 'date_started_judging'],
        };

        $draftData = collect($mainFields)
            ->mapWithKeys(fn (string $field) => [$field => $profile->getAttribute($field)])
            ->filter(fn ($value) => $value !== null)
            ->toArray();

        $service->saveDraft($profile, $draftData);
    }
}
