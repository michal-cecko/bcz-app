<?php

namespace App\Filament\Resources\Users;

use App\Enums\DraftStatusEnum;
use App\Enums\RoleEnum;
use App\Filament\Resources\Users\Pages\CreateUser;
use App\Filament\Resources\Users\Pages\EditUser;
use App\Filament\Resources\Users\Pages\ListUsers;
use App\Filament\Resources\Users\Pages\ViewUser;
use App\Filament\Resources\Users\Schemas\UserForm;
use App\Filament\Resources\Users\Tables\UsersTable;
use App\Models\User;
use BackedEnum;
use Filament\Infolists\Components\SpatieMediaLibraryImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\HtmlString;
use Spatie\Permission\Models\Role;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?string $modelLabel = 'používateľa';

    protected static ?string $pluralModelLabel = 'Používatelia';

    protected static bool $hasTitleCaseModelLabel = false;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUsers;

    protected static string|\UnitEnum|null $navigationGroup = 'Organizácia';

    protected static ?int $navigationSort = 90;

    public static function shouldRegisterNavigation(): bool
    {
        return ! auth()->user()?->isMemberLevel();
    }

    /**
     * Platform admins see every user (including those on other teams or no team).
     * Everyone else stays scoped to the current tenant.
     */
    public static function scopeEloquentQueryToTenant(Builder $query, ?Model $tenant): Builder
    {
        /** @var User|null $actor */
        $actor = auth()->user();

        if ($actor?->isGlobalAdmin()) {
            return $query;
        }

        return parent::scopeEloquentQueryToTenant($query, $tenant);
    }

    public static function form(Schema $schema): Schema
    {
        return UserForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('user_view_tabs')
                    ->tabs([
                        Tab::make('Osobné údaje')
                            ->schema([
                                Grid::make(['default' => 1, 'lg' => 3])
                                    ->schema([
                                        // Left: Profile image (1/3)
                                        SpatieMediaLibraryImageEntry::make('profile_image')
                                            ->collection('profile_image')
                                            ->label('Profilový obrázok')
                                            ->defaultImageUrl(fn ($record) => 'https://ui-avatars.com/api/?name='.urlencode($record->name).'&color=7F9CF5&background=EBF4FF')
                                            ->columnSpan(1),

                                        // Right: Info fields (2/3)
                                        Grid::make(2)
                                            ->columnSpan(['default' => 1, 'lg' => 2])
                                            ->schema([
                                                TextEntry::make('first_name')
                                                    ->label('Meno'),
                                                TextEntry::make('last_name')
                                                    ->label('Priezvisko'),
                                                TextEntry::make('email')
                                                    ->label('E-mail')
                                                    ->copyable(),
                                                TextEntry::make('all_roles')
                                                    ->label('Roly')
                                                    ->badge()
                                                    ->state(function (User $record): array {
                                                        $globalRoles = $record->getRoleNames()
                                                            ->reject(fn ($r) => $r === 'panel_user')
                                                            ->values();

                                                        $tenant = filament()->getTenant();
                                                        $teamRoles = $tenant
                                                            ? $record->teams()
                                                                ->where('teams.id', $tenant->id)
                                                                ->pluck('team_user.role')
                                                                ->map(fn ($r) => $r instanceof RoleEnum ? $r->value : $r)
                                                            : collect();

                                                        return $globalRoles->merge($teamRoles)->unique()->values()->toArray();
                                                    })
                                                    ->formatStateUsing(fn (string $state): string => RoleEnum::tryFrom($state)?->getLabel() ?? $state),
                                                TextEntry::make('gender')
                                                    ->label('Pohlavie'),
                                                TextEntry::make('birth_date')
                                                    ->label('Dátum narodenia')
                                                    ->date(),
                                                TextEntry::make('email_verified_at')
                                                    ->label('E-mail overený')
                                                    ->dateTime()
                                                    ->placeholder('Neoverený'),
                                                TextEntry::make('created_at')
                                                    ->label('Vytvorený')
                                                    ->dateTime(),
                                            ]),
                                    ]),
                            ]),
                        Tab::make('Verejné profily')
                            ->schema([
                                self::buildInfolistProfileSection('coach'),
                                self::buildInfolistProfileSection('athlete'),
                            ]),
                    ])
                    ->persistTabInQueryString()
                    ->columnSpanFull(),
            ]);
    }

    protected static function buildInfolistProfileSection(string $role): Section
    {
        $config = match ($role) {
            'coach' => [
                'title' => 'Profil trénera',
                'col' => 'coach_profile_approved_at',
                'rel' => 'coachProfile',
                'route' => 'coach.show',
                'fields' => ['biography' => 'Biografia', 'date_started_coaching' => 'Začiatok trénerskej kariéry'],
                'visibleFn' => fn (User $r) => $r->teams()->wherePivot('role', RoleEnum::COACH->value)->exists(),
            ],
            'athlete' => [
                'title' => 'Profil športovca',
                'col' => 'athlete_profile_approved_at',
                'rel' => 'athleteProfile',
                'route' => 'athlete.show',
                'fields' => ['journey_text' => 'Môj príbeh', 'date_started_working_out' => 'Začiatok cvičenia'],
                'visibleFn' => fn (User $r) => $r->teams()->wherePivot('role', RoleEnum::ATHLETE->value)->exists(),
            ],
        };

        $profileEntries = [];
        foreach ($config['fields'] as $field => $label) {
            if ($field === 'disciplines') {
                $profileEntries[] = TextEntry::make("{$config['rel']}.{$field}")
                    ->label($label)
                    ->badge()
                    ->state(fn (User $record) => $record->{$config['rel']}?->disciplines ?? []);
            } elseif (str_contains($field, 'date_')) {
                $profileEntries[] = TextEntry::make("{$config['rel']}.{$field}")
                    ->label($label)
                    ->date()
                    ->placeholder('—');
            } else {
                $profileEntries[] = TextEntry::make("{$config['rel']}.{$field}")
                    ->label($label)
                    ->html()
                    ->state(fn (User $record) => $record->{$config['rel']}?->getTranslation($field, app()->getLocale()) ?? '—');
            }
        }

        return Section::make($config['title'])
            ->collapsed()
            ->visible($config['visibleFn'])
            ->schema([
                Grid::make(2)->schema([
                    TextEntry::make("{$role}_profile_status")
                        ->label('Stav')
                        ->state(function (User $record) use ($config) {
                            $profile = $record->{$config['rel']};
                            $approved = $record->{$config['col']};
                            $draft = $profile?->draft_status;

                            if ($draft === DraftStatusEnum::Rejected) {
                                return 'Zamietnutý';
                            }
                            if ($draft === DraftStatusEnum::Pending) {
                                return 'Čaká na schválenie';
                            }
                            if ($approved) {
                                return 'Schválený';
                            }

                            return 'Neaktívny';
                        })
                        ->badge()
                        ->color(fn (string $state): string => match ($state) {
                            'Schválený' => 'success',
                            'Čaká na schválenie' => 'warning',
                            'Zamietnutý' => 'danger',
                            default => 'gray',
                        }),
                    TextEntry::make("{$role}_profile_link")
                        ->label('Link na profil')
                        ->visible(fn (User $record) => $record->{$config['col']} !== null)
                        ->state(function (User $record) use ($config) {
                            $url = route($config['route'], $record);

                            return new HtmlString(
                                "<a href='{$url}' target='_blank' class='fi-btn fi-btn-size-sm fi-color-gray inline-flex items-center gap-1.5 rounded-lg px-3 py-2 text-sm font-semibold shadow-sm bg-gray-100 text-gray-700 ring-1 ring-gray-300 hover:bg-gray-200 dark:bg-white/5 dark:text-white dark:ring-white/10 dark:hover:bg-white/10 transition'>".
                                "<svg class='w-4 h-4' xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke-width='1.5' stroke='currentColor'><path stroke-linecap='round' stroke-linejoin='round' d='M13.5 6H5.25A2.25 2.25 0 0 0 3 8.25v10.5A2.25 2.25 0 0 0 5.25 21h10.5A2.25 2.25 0 0 0 18 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25' /></svg>".
                                'Zobraziť verejný profil</a>'
                            );
                        })
                        ->html(),
                ]),
                ...$profileEntries,
            ]);
    }

    public static function table(Table $table): Table
    {
        return UsersTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\MembershipsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListUsers::route('/'),
            'create' => CreateUser::route('/create'),
            'view' => ViewUser::route('/{record}'),
            'edit' => EditUser::route('/{record}/edit'),
        ];
    }

    /**
     * Split selected Spatie role IDs into global (stay on model_has_roles) and team-scoped
     * (replace rows on each given team only — teams not in $teamIds keep their existing pivots).
     *
     * @param  array<int|string>  $roleIds
     * @param  array<int, string>|string|null  $teamIds  Single ID, list of IDs, or null to skip pivot sync.
     */
    public static function syncTeamScopedRoles(User $user, array $roleIds, array|string|null $teamIds): void
    {
        $roles = $roleIds ? Role::query()->whereIn('id', $roleIds)->get() : collect();
        $teamScopedValues = array_map(fn (RoleEnum $r) => $r->value, RoleEnum::teamScopedCases());

        [$teamScoped, $global] = $roles->partition(fn ($r) => in_array($r->name, $teamScopedValues, true));

        // Keep only the chosen global roles (Spatie-managed). The form hides
        // `panel_user` and SUPER_ADMIN from the Select; preserve any of those
        // already assigned so a save doesn't silently revoke them.
        $hiddenAssigned = $user->roles()
            ->whereIn('name', UserForm::hiddenRoleNames())
            ->pluck('name')
            ->all();

        $user->syncRoles(array_values(array_unique(array_merge(
            $global->pluck('name')->all(),
            $hiddenAssigned,
        ))));

        $teamIds = array_values(array_filter(is_array($teamIds) ? $teamIds : [$teamIds]));

        if (empty($teamIds)) {
            return;
        }

        // Rebuild rows ONLY for the selected teams; teams outside this list are untouched.
        foreach ($teamIds as $teamId) {
            DB::table('team_user')
                ->where('user_id', $user->id)
                ->where('team_id', $teamId)
                ->delete();

            foreach ($teamScoped as $role) {
                DB::table('team_user')->insert([
                    'team_id' => $teamId,
                    'user_id' => $user->id,
                    'role' => $role->name,
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
