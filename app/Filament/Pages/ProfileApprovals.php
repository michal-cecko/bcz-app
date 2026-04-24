<?php

namespace App\Filament\Pages;

use App\Enums\DraftStatusEnum;
use App\Enums\RoleEnum;
use App\Filament\Resources\Users\UserResource;
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
use Illuminate\Support\HtmlString;

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
                    ->searchable()
                    ->url(fn (User $record): ?string => UserResource::getUrl('view', ['record' => $record], tenant: Filament::getTenant()))
                    ->color('primary')
                    ->weight('semibold'),
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
                Action::make('details')
                    ->label('Detail')
                    ->icon(Heroicon::OutlinedEye)
                    ->color('gray')
                    ->modalHeading(fn (User $record): string => "Čakajúce zmeny · {$record->name}")
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Zavrieť')
                    ->modalContent(fn (User $record): HtmlString => new HtmlString($this->renderDraftDetails($record))),
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

    protected function renderDraftDetails(User $record): string
    {
        $sections = [];

        if ($record->coachProfile?->draft_status === DraftStatusEnum::Pending) {
            $sections[] = $this->renderProfileDraftSection('Tréner', $record->coachProfile, [
                'biography' => ['label' => 'Biografia', 'translatable' => true],
                'date_started_coaching' => ['label' => 'Začiatok trénerskej kariéry', 'translatable' => false],
            ]);
        }

        if ($record->athleteProfile?->draft_status === DraftStatusEnum::Pending) {
            $sections[] = $this->renderProfileDraftSection('Športovec', $record->athleteProfile, [
                'journey_text' => ['label' => 'Môj príbeh', 'translatable' => true],
                'date_started_working_out' => ['label' => 'Začiatok cvičenia', 'translatable' => false],
            ]);
        }

        if (empty($sections)) {
            return '<p class="text-sm text-gray-500 dark:text-gray-400">Žiadne čakajúce zmeny.</p>';
        }

        return implode('', $sections);
    }

    /**
     * @param  array<string, array{label: string, translatable: bool}>  $fields
     */
    protected function renderProfileDraftSection(string $roleLabel, mixed $profile, array $fields): string
    {
        $draft = is_array($profile->draft_data) ? $profile->draft_data : [];
        $rows = '';

        foreach ($fields as $field => $meta) {
            $newValue = $this->formatDraftValue($draft[$field] ?? null, $meta['translatable']);
            $currentValue = $this->formatDraftValue($profile->getAttribute($field), $meta['translatable']);

            $rows .= sprintf(
                '<tr class="align-top border-t border-gray-200 dark:border-white/10">'
                .'<td class="py-3 pr-4 text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400 w-1/3">%s</td>'
                .'<td class="py-3 pr-4 text-sm text-gray-500 dark:text-gray-400 line-through">%s</td>'
                .'<td class="py-3 text-sm text-gray-900 dark:text-white font-medium">%s</td>'
                .'</tr>',
                e($meta['label']),
                $currentValue,
                $newValue,
            );
        }

        $submittedAt = $profile->draft_submitted_at?->diffForHumans();

        return sprintf(
            '<section class="mb-6 rounded-xl border border-gray-200 dark:border-white/10 bg-gray-50 dark:bg-white/5 p-5">'
            .'<header class="flex items-center justify-between mb-4">'
            .'<h3 class="text-base font-bold text-gray-900 dark:text-white">Profil: %s</h3>'
            .'%s'
            .'</header>'
            .'<table class="w-full text-left"><thead><tr class="text-xs font-semibold uppercase tracking-wider text-gray-400">'
            .'<th class="pb-2 w-1/3">Pole</th><th class="pb-2">Aktuálne</th><th class="pb-2">Navrhované</th>'
            .'</tr></thead><tbody>%s</tbody></table>'
            .'</section>',
            e($roleLabel),
            $submittedAt ? '<span class="text-xs text-gray-500 dark:text-gray-400">Odoslané '.e($submittedAt).'</span>' : '',
            $rows,
        );
    }

    protected function formatDraftValue(mixed $value, bool $translatable): string
    {
        if ($value === null || $value === '' || $value === []) {
            return '<span class="text-gray-400 italic">—</span>';
        }

        if ($translatable && is_array($value)) {
            $parts = [];
            foreach ($value as $locale => $text) {
                if ($text === null || $text === '') {
                    continue;
                }
                $parts[] = '<div><span class="text-xs font-semibold uppercase text-gray-400 mr-2">'.e($locale).'</span>'.e($this->stripHtml((string) $text)).'</div>';
            }

            return $parts ? implode('', $parts) : '<span class="text-gray-400 italic">—</span>';
        }

        if ($value instanceof \DateTimeInterface) {
            return e($value->format('d.m.Y'));
        }

        if (is_array($value)) {
            return e(implode(', ', $value));
        }

        return e($this->stripHtml((string) $value));
    }

    protected function stripHtml(string $value): string
    {
        return trim(html_entity_decode(strip_tags($value)));
    }

    protected function getTableQuery(): Builder
    {
        /** @var User $currentUser */
        $currentUser = auth()->user();

        $query = User::query()
            ->with(['coachProfile', 'athleteProfile'])
            ->where(function (Builder $q) {
                $q->whereHas('coachProfile', fn (Builder $q) => $q->where('draft_status', DraftStatusEnum::Pending))
                    ->orWhereHas('athleteProfile', fn (Builder $q) => $q->where('draft_status', DraftStatusEnum::Pending));
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
