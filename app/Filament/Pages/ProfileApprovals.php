<?php

namespace App\Filament\Pages;

use App\Enums\DraftStatusEnum;
use App\Enums\RoleEnum;
use App\Models\User;
use App\Services\ProfileDraftService;
use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ProfileApprovals extends Page implements HasTable
{
    use InteractsWithTable;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocumentCheck;

    protected static ?string $navigationLabel = 'Schválenie profilov';

    protected static ?string $title = 'Schválenie profilov';

    protected static string|\UnitEnum|null $navigationGroup = 'Organizácia';

    protected static ?int $navigationSort = 10;

    protected static bool $isScopedToTenant = false;

    protected string $view = 'filament.pages.profile-approvals';

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

    public static function getNavigationBadge(): ?string
    {
        $count = User::where(function (Builder $q) {
            $q->whereHas('coachProfile', fn (Builder $q) => $q->where('draft_status', DraftStatusEnum::Pending))
                ->orWhereHas('athleteProfile', fn (Builder $q) => $q->where('draft_status', DraftStatusEnum::Pending));
        })->count();

        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): string|array|null
    {
        return 'warning';
    }

    public function table(Table $table): Table
    {
        return $table
            ->query($this->getTableQuery())
            ->columns([
                TextColumn::make('name')
                    ->label('Používateľ')
                    ->searchable(),
                TextColumn::make('pending_roles')
                    ->label('Rola')
                    ->state(function (User $record): string {
                        $roles = [];
                        if ($record->coachProfile?->draft_status === DraftStatusEnum::Pending) {
                            $roles[] = 'Tréner';
                        }
                        if ($record->athleteProfile?->draft_status === DraftStatusEnum::Pending) {
                            $roles[] = 'Športovec';
                        }

                        return implode(', ', $roles);
                    })
                    ->badge(),
                TextColumn::make('draft_submitted_at')
                    ->label('Odoslané')
                    ->state(function (User $record): ?string {
                        $dates = array_filter([
                            $record->coachProfile?->draft_submitted_at,
                            $record->athleteProfile?->draft_submitted_at,
                        ]);

                        return ! empty($dates) ? max($dates)->diffForHumans() : null;
                    }),
                TextColumn::make('previously_approved')
                    ->label('Už schválený')
                    ->state(fn (User $record): string => ($record->coach_profile_approved_at || $record->athlete_profile_approved_at) ? 'Áno' : 'Nie')
                    ->badge()
                    ->color(fn (string $state): string => $state === 'Áno' ? 'success' : 'gray'),
            ])
            ->recordActions([
                Action::make('approve')
                    ->label('Schváliť')
                    ->icon(Heroicon::OutlinedCheckCircle)
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Schváliť profil')
                    ->modalDescription('Schváliť všetky čakajúce profily tohto používateľa?')
                    ->action(function (User $record): void {
                        $service = new ProfileDraftService;

                        if ($record->coachProfile?->draft_status === DraftStatusEnum::Pending) {
                            $service->approveDraft($record->coachProfile, $record);
                        }
                        if ($record->athleteProfile?->draft_status === DraftStatusEnum::Pending) {
                            $service->approveDraft($record->athleteProfile, $record);
                        }

                        Notification::make()
                            ->success()
                            ->title("Profil {$record->name} bol schválený")
                            ->send();
                    }),
                Action::make('reject')
                    ->label('Zamietnuť')
                    ->icon(Heroicon::OutlinedXCircle)
                    ->color('danger')
                    ->schema([
                        Textarea::make('reason')
                            ->label('Dôvod zamietnutia')
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

                        Notification::make()
                            ->warning()
                            ->title("Profil {$record->name} bol zamietnutý")
                            ->send();
                    }),
            ])
            ->emptyStateHeading('Žiadne čakajúce profily')
            ->emptyStateDescription('Všetky profily boli spracované.')
            ->emptyStateIcon(Heroicon::OutlinedCheckCircle);
    }

    protected function getTableQuery(): Builder
    {
        /** @var User $currentUser */
        $currentUser = auth()->user();

        $query = User::query()
            ->with(['coachProfile', 'athleteProfile'])
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
                $query->where(function (Builder $q) {
                    $q->whereHas('coachProfile', fn (Builder $q) => $q->where('draft_status', DraftStatusEnum::Pending))
                        ->orWhereHas('athleteProfile', fn (Builder $q) => $q->where('draft_status', DraftStatusEnum::Pending));
                });
            }
        }

        return $query;
    }
}
