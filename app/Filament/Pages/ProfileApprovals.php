<?php

namespace App\Filament\Pages;

use App\Enums\DraftStatusEnum;
use App\Enums\RoleEnum;
use App\Models\User;
use App\Services\ProfileDraftService;
use Filament\Facades\Filament;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ProfileApprovals extends Page implements HasTable
{
    use InteractsWithTable;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocumentCheck;

    protected static ?string $navigationLabel = 'Schvalenie profilov';

    protected static ?string $title = 'Schvalenie profilov';

    protected static string|\UnitEnum|null $navigationGroup = 'Organizacia';

    protected static ?int $navigationSort = 10;

    protected static bool $isScopedToTenant = false;

    public static function shouldRegisterNavigation(): bool
    {
        /** @var User|null $user */
        $user = auth()->user();
        if (! $user) {
            return false;
        }

        return $user->hasRole([RoleEnum::SUPER_ADMIN, RoleEnum::ADMIN])
            || $user->hasTeamRole(RoleEnum::TEAM_ADMIN);
    }

    public function table(Table $table): Table
    {
        return $table
            ->query($this->getTableQuery())
            ->columns([
                TextColumn::make('name')
                    ->label('Pouzivatel')
                    ->searchable(),
                TextColumn::make('pending_roles')
                    ->label('Rola')
                    ->state(function (User $record): string {
                        $roles = [];
                        if ($record->coachProfile?->draft_status === DraftStatusEnum::Pending) {
                            $roles[] = 'Trener';
                        }
                        if ($record->athleteProfile?->draft_status === DraftStatusEnum::Pending) {
                            $roles[] = 'Sportovec';
                        }
                        if ($record->judgeProfile?->draft_status === DraftStatusEnum::Pending) {
                            $roles[] = 'Porotca';
                        }

                        return implode(', ', $roles);
                    })
                    ->badge(),
                TextColumn::make('draft_submitted_at')
                    ->label('Odoslane')
                    ->state(function (User $record): ?string {
                        $dates = array_filter([
                            $record->coachProfile?->draft_submitted_at,
                            $record->athleteProfile?->draft_submitted_at,
                            $record->judgeProfile?->draft_submitted_at,
                        ]);

                        return ! empty($dates) ? max($dates)->diffForHumans() : null;
                    }),
                TextColumn::make('previously_approved')
                    ->label('Uz schvaleny')
                    ->state(fn (User $record): string => ($record->coach_profile_approved_at || $record->athlete_profile_approved_at || $record->judge_profile_approved_at) ? 'Ano' : 'Nie')
                    ->badge()
                    ->color(fn (string $state): string => $state === 'Ano' ? 'success' : 'gray'),
            ])
            ->recordActions([
                Action::make('approve')
                    ->label('Schvalit')
                    ->icon(Heroicon::OutlinedCheckCircle)
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Schvalit profil')
                    ->modalDescription('Schvalit vsetky cakajuce profily tohto pouzivatela?')
                    ->action(function (User $record): void {
                        $service = new ProfileDraftService;

                        if ($record->coachProfile?->draft_status === DraftStatusEnum::Pending) {
                            $service->approveDraft($record->coachProfile, $record);
                        }
                        if ($record->athleteProfile?->draft_status === DraftStatusEnum::Pending) {
                            $service->approveDraft($record->athleteProfile, $record);
                        }
                        if ($record->judgeProfile?->draft_status === DraftStatusEnum::Pending) {
                            $service->approveDraft($record->judgeProfile, $record);
                        }

                        Notification::make()
                            ->success()
                            ->title("Profil {$record->name} bol schvaleny")
                            ->send();
                    }),
                Action::make('reject')
                    ->label('Zamietnut')
                    ->icon(Heroicon::OutlinedXCircle)
                    ->color('danger')
                    ->schema([
                        Textarea::make('reason')
                            ->label('Dovod zamietnutia')
                            ->required()
                            ->rows(3),
                    ])
                    ->action(function (User $record, array $data): void {
                        $service = new ProfileDraftService;

                        if ($record->coachProfile?->draft_status === DraftStatusEnum::Pending) {
                            $service->rejectDraft($record->coachProfile, $data['reason']);
                        }
                        if ($record->athleteProfile?->draft_status === DraftStatusEnum::Pending) {
                            $service->rejectDraft($record->athleteProfile, $data['reason']);
                        }
                        if ($record->judgeProfile?->draft_status === DraftStatusEnum::Pending) {
                            $service->rejectDraft($record->judgeProfile, $data['reason']);
                        }

                        Notification::make()
                            ->warning()
                            ->title("Profil {$record->name} bol zamietnuty")
                            ->send();
                    }),
            ])
            ->emptyStateHeading('Ziadne cakajuce profily')
            ->emptyStateDescription('Vsetky profily boli spracovane.')
            ->emptyStateIcon(Heroicon::OutlinedCheckCircle);
    }

    protected function getTableQuery(): Builder
    {
        /** @var User $currentUser */
        $currentUser = auth()->user();

        $query = User::query()
            ->with(['coachProfile', 'athleteProfile', 'judgeProfile'])
            ->where(function (Builder $q) {
                $q->whereHas('coachProfile', fn (Builder $q) => $q->where('draft_status', DraftStatusEnum::Pending))
                    ->orWhereHas('athleteProfile', fn (Builder $q) => $q->where('draft_status', DraftStatusEnum::Pending))
                    ->orWhereHas('judgeProfile', fn (Builder $q) => $q->where('draft_status', DraftStatusEnum::Pending));
            });

        // TEAM_ADMIN can only see their team's members (not judges)
        if ($currentUser->hasTeamRole(RoleEnum::TEAM_ADMIN) && ! $currentUser->hasRole([RoleEnum::SUPER_ADMIN, RoleEnum::ADMIN])) {
            $teamId = Filament::getTenant()?->id;
            if ($teamId) {
                $query->whereHas('teams', fn (Builder $q) => $q->where('teams.id', $teamId));
                // Exclude judge-only pending profiles for team admins
                $query->where(function (Builder $q) {
                    $q->whereHas('coachProfile', fn (Builder $q) => $q->where('draft_status', DraftStatusEnum::Pending))
                        ->orWhereHas('athleteProfile', fn (Builder $q) => $q->where('draft_status', DraftStatusEnum::Pending));
                });
            }
        }

        return $query;
    }
}
