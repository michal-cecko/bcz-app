<?php

namespace App\Filament\Resources\Users\Schemas;

use App\Enums\DraftStatusEnum;
use App\Enums\GenderEnum;
use App\Enums\RoleEnum;
use App\Filament\Schemas\PublicProfileSchema;
use App\Models\Team;
use App\Models\User;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Components\View;
use Filament\Schemas\Schema;
use Illuminate\Support\HtmlString;
use Spatie\Permission\Models\Role;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('user_tabs')
                    ->tabs([
                        self::personalInfoTab(),
                        self::passwordTab()->visible(fn (string $operation): bool => $operation === 'edit'),
                        self::publicProfilesTab(),
                        self::passkeysTab()->visible(fn (string $operation, ?User $record): bool => $operation === 'edit' && $record?->id === auth()->id()),
                    ])
                    ->persistTabInQueryString()
                    ->columnSpanFull(),
            ]);
    }

    protected static function personalInfoTab(): Tab
    {
        return Tab::make('Osobné údaje')
            ->schema([
                Grid::make(['default' => 1, 'lg' => 3])
                    ->schema([
                        SpatieMediaLibraryFileUpload::make('profile_image')
                            ->collection('profile_image')
                            ->disk('public')
                            ->visibility('public')
                            ->label('Profilový obrázok')
                            ->image()
                            ->imagePreviewHeight('212')
                            ->columnSpan(1),

                        Grid::make(2)
                            ->columnSpan(['default' => 1, 'lg' => 2])
                            ->schema([
                                TextInput::make('first_name')
                                    ->label('Meno')
                                    ->required(),
                                TextInput::make('last_name')
                                    ->label('Priezvisko')
                                    ->required(),
                                TextInput::make('email')
                                    ->label('E-mail')
                                    ->email()
                                    ->required()
                                    ->unique(ignoreRecord: true)
                                    ->validationMessages([
                                        'unique' => 'Používateľ s touto e-mailovou adresou už existuje.',
                                    ]),
                                Select::make('roles')
                                    ->label('Roly')
                                    ->dehydrated(false)
                                    ->visible(fn (?User $record): bool => self::canEditPrivilegedFields($record))
                                    ->options(
                                        Role::query()
                                            ->whereNotIn('name', self::hiddenRoleNames())
                                            ->pluck('name', 'id')
                                            ->map(fn (string $name): string => RoleEnum::tryFrom($name)?->getLabel() ?? $name)
                                    )
                                    ->multiple()
                                    ->preload()
                                    ->live()
                                    ->afterStateHydrated(function (Select $component, ?User $record): void {
                                        if (! $record) {
                                            return;
                                        }

                                        // Global roles live on Spatie model_has_roles, team-scoped roles on team_user pivot.
                                        // Surface both as selected option IDs in the roles Select — but only IDs that
                                        // are actually in the visible options list. Roles like `panel_user` or
                                        // SUPER_ADMIN are managed elsewhere; including them here would render the
                                        // raw numeric ID as a chip (no matching option to look up the label).
                                        $visibleIds = Role::query()
                                            ->whereNotIn('name', self::hiddenRoleNames())
                                            ->pluck('id')
                                            ->all();

                                        $globalRoleIds = $record->roles()->pluck('id')->all();

                                        $teamRoleNames = $record->teams()
                                            ->pluck('team_user.role')
                                            ->unique()
                                            ->filter()
                                            ->all();
                                        $teamRoleIds = $teamRoleNames
                                            ? Role::query()->whereIn('name', $teamRoleNames)->pluck('id')->all()
                                            : [];

                                        $allIds = array_values(array_unique(array_merge($globalRoleIds, $teamRoleIds)));
                                        $component->state(array_values(array_intersect($allIds, $visibleIds)));
                                    })
                                    ->afterStateUpdated(function (Set $set, $state): void {
                                        // Non-members (admin/editor only) always get the free flag forced on.
                                        if (! self::hasMembershipEligibleRole($state ?? [])) {
                                            $set('has_free_membership', true);
                                        }
                                    })
                                    ->required(),
                                Select::make('team_ids')
                                    ->label('Tímy')
                                    ->helperText('Povinné pre rolu Tímový admin, Tréner alebo Športovec. Vybrané tímy získajú zvolené tímové roly.')
                                    ->visible(fn (?User $record): bool => self::canEditPrivilegedFields($record))
                                    ->options(fn () => Team::query()->pluck('name', 'id')->map(fn ($name) => is_array($name) ? ($name['sk'] ?? reset($name)) : $name))
                                    ->multiple()
                                    ->searchable()
                                    ->preload()
                                    ->dehydrated(false)
                                    ->afterStateHydrated(function (Select $component, ?User $record): void {
                                        if ($record) {
                                            $component->state($record->teams()->pluck('teams.id')->all());
                                        }
                                    })
                                    ->required(fn (Get $get): bool => self::hasTeamScopedRole($get('roles') ?? []))
                                    ->rule(function (Get $get) {
                                        return function (string $attribute, $value, \Closure $fail) use ($get): void {
                                            if (self::hasTeamScopedRole($get('roles') ?? []) && empty($value)) {
                                                $fail('Pre tímovú rolu je potrebné zvoliť aspoň jeden tím.');
                                            }
                                        };
                                    }),
                                Select::make('gender')
                                    ->label('Pohlavie')
                                    ->options(GenderEnum::translations()),
                                DatePicker::make('birth_date')
                                    ->label('Dátum narodenia')
                                    ->native(false)
                                    ->maxDate(now()),
                                Toggle::make('send_welcome_notification')
                                    ->label('Poslať privítaciu notifikáciu')
                                    ->helperText('Po vytvorení odošleme používateľovi e-mail s prihlasovacím odkazom a nastavením hesla.')
                                    ->default(true)
                                    ->dehydrated(false)
                                    ->visible(fn (string $operation): bool => $operation === 'create'),
                                Toggle::make('has_free_membership')
                                    ->label('Oslobodený od platby členstva')
                                    ->helperText('Pri otvorení sezóny dostane bezplatné členstvo bez notifikácie. Automaticky zapnuté pre admina, editora a porotcu.')
                                    ->default(false)
                                    ->visible(fn (?User $record): bool => self::canEditPrivilegedFields($record))
                                    ->dehydrated(fn (?User $record): bool => self::canEditPrivilegedFields($record))
                                    ->disabled(fn (Get $get): bool => ! self::hasMembershipEligibleRole($get('roles') ?? []))
                                    ->afterStateHydrated(function (Toggle $component, Get $get, $state) {
                                        if (! self::hasMembershipEligibleRole($get('roles') ?? [])) {
                                            $component->state(true);
                                        }
                                    })
                                    ->dehydrateStateUsing(fn (Get $get, $state) => self::hasMembershipEligibleRole($get('roles') ?? []) ? (bool) $state : true),
                            ]),
                    ]),
            ]);
    }

    protected static function passwordTab(): Tab
    {
        return Tab::make('Heslo')
            ->schema([
                Grid::make(2)
                    ->schema([
                        TextInput::make('password')
                            ->label('Nové heslo')
                            ->helperText('Nechajte prázdne ak nechcete meniť heslo.')
                            ->password()
                            ->revealable()
                            ->dehydrated(fn (?string $state): bool => filled($state))
                            ->requiredWith('passwordConfirmation')
                            ->same('passwordConfirmation'),
                        TextInput::make('passwordConfirmation')
                            ->label('Potvrdenie hesla')
                            ->password()
                            ->revealable()
                            ->dehydrated(false)
                            ->requiredWith('password'),
                    ]),
            ]);
    }

    protected static function passkeysTab(): Tab
    {
        return Tab::make('Passkeys')
            ->icon('heroicon-o-finger-print')
            ->schema([
                View::make('filament.components.passkeys-section'),
            ]);
    }

    protected static function publicProfilesTab(): Tab
    {
        return Tab::make('Verejné profily')
            ->visible(fn ($record) => $record && count($record->getProfileableRoles()) > 0)
            ->schema([
                Tabs::make('profile_role_tabs')
                    ->tabs([
                        self::buildRoleTab('coach'),
                        self::buildRoleTab('athlete'),
                    ])
                    ->persistTabInQueryString('profile-role'),
            ]);
    }

    protected static function buildRoleTab(string $role): Tab
    {
        $config = match ($role) {
            'coach' => [
                'label' => 'Tréner',
                'icon' => 'heroicon-o-academic-cap',
                'approvalCol' => 'coach_profile_approved_at',
                'profileRel' => 'coachProfile',
                'visibleFn' => fn ($record) => $record && $record->teams()->wherePivot('role', RoleEnum::COACH->value)->exists(),
            ],
            'athlete' => [
                'label' => 'Športovec',
                'icon' => 'heroicon-o-trophy',
                'approvalCol' => 'athlete_profile_approved_at',
                'profileRel' => 'athleteProfile',
                'visibleFn' => fn ($record) => $record && $record->teams()->wherePivot('role', RoleEnum::ATHLETE->value)->exists(),
            ],
        };

        $subtabs = self::buildResourceSubTabs($role);

        return Tab::make($config['label'])
            ->icon($config['icon'])
            ->badge(function ($record) use ($config): ?string {
                if (! $record) {
                    return null;
                }

                $profile = $record->{$config['profileRel']};
                $draftStatus = $profile?->draft_status;

                if ($draftStatus === DraftStatusEnum::Rejected) {
                    return 'Zamietnutý';
                }
                if ($draftStatus === DraftStatusEnum::Pending) {
                    return 'Čaká na schválenie';
                }
                if ($record->{$config['approvalCol']}) {
                    return 'Schválený';
                }

                return 'Neaktívny';
            })
            ->badgeColor(function ($record) use ($config): string {
                if (! $record) {
                    return 'gray';
                }

                $profile = $record->{$config['profileRel']};
                $draftStatus = $profile?->draft_status;

                if ($draftStatus === DraftStatusEnum::Rejected) {
                    return 'danger';
                }
                if ($draftStatus === DraftStatusEnum::Pending) {
                    return 'warning';
                }
                if ($record->{$config['approvalCol']}) {
                    return 'success';
                }

                return 'gray';
            })
            ->visible($config['visibleFn'])
            ->schema([
                Tabs::make("{$role}_subtabs")
                    ->tabs($subtabs)
                    ->persistTabInQueryString("{$role}-tab"),
            ]);
    }

    /**
     * Build sub-tabs for UserResource with ->relationship() on repeaters and profile section.
     *
     * @return list<Tab>
     */
    protected static function buildResourceSubTabs(string $role): array
    {
        $profileConfig = match ($role) {
            'coach' => ['approvalCol' => 'coach_profile_approved_at', 'relation' => 'coachProfile', 'route' => 'coach.show'],
            'athlete' => ['approvalCol' => 'athlete_profile_approved_at', 'relation' => 'athleteProfile', 'route' => 'athlete.show'],
        };

        $profileLabel = $role === 'athlete' ? 'Môj príbeh' : 'Profil';

        $tabs = [
            Tab::make($profileLabel)
                ->icon('heroicon-o-user')
                ->schema([
                    Placeholder::make("{$role}_link")
                        ->label('')
                        ->visible(fn ($record) => $record?->{$profileConfig['approvalCol']} !== null)
                        ->content(function ($record) use ($profileConfig) {
                            if (! $record) {
                                return '';
                            }
                            $url = route($profileConfig['route'], $record);

                            return new HtmlString(
                                "<a href='{$url}' target='_blank' class='fi-btn fi-btn-size-sm fi-color-gray inline-flex items-center gap-1.5 rounded-lg px-3 py-2 text-sm font-semibold shadow-sm bg-gray-100 text-gray-700 ring-1 ring-gray-300 hover:bg-gray-200 dark:bg-white/5 dark:text-white dark:ring-white/10 dark:hover:bg-white/10 transition'>".
                                "<svg class='w-4 h-4' xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke-width='1.5' stroke='currentColor'><path stroke-linecap='round' stroke-linejoin='round' d='M13.5 6H5.25A2.25 2.25 0 0 0 3 8.25v10.5A2.25 2.25 0 0 0 5.25 21h10.5A2.25 2.25 0 0 0 18 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25' /></svg>".
                                'Zobraziť verejný profil</a>'
                            );
                        }),
                    Section::make()
                        ->relationship($profileConfig['relation'])
                        ->schema(PublicProfileSchema::profileFields($role)),
                ]),
        ];

        if ($role === 'coach') {
            $tabs[] = Tab::make('Certifikáty')
                ->icon('heroicon-o-academic-cap')
                ->schema([PublicProfileSchema::certificationsRepeater()->relationship()]);
        }

        if ($role === 'athlete') {
            $tabs[] = Tab::make('Cesta k prvkom')
                ->icon('heroicon-o-bolt')
                ->schema([
                    PublicProfileSchema::exercisesRepeater()->relationship(),
                ]);
            $tabs[] = Tab::make('Moje ciele')
                ->icon('heroicon-o-flag')
                ->schema([PublicProfileSchema::goalsRepeater()->relationship()]);
        }

        $tabs[] = Tab::make('Galéria')
            ->icon('heroicon-o-photo')
            ->schema([
                Section::make()
                    ->relationship($profileConfig['relation'])
                    ->schema([PublicProfileSchema::galleryUpload()]),
            ]);

        return $tabs;
    }

    /**
     * Roles that the form intentionally hides from the Roles Select. Filament
     * panel access (`panel_user`) is bookkeeping; SUPER_ADMIN is reserved and
     * granted by other means. Both must be preserved across saves so a hidden
     * role isn't silently revoked when the form is submitted.
     *
     * @return list<string>
     */
    public static function hiddenRoleNames(): array
    {
        return ['panel_user', RoleEnum::SUPER_ADMIN->value];
    }

    /**
     * Returns true if the authenticated user is allowed to manage privileged
     * fields (Roles, Teams, has_free_membership) on the given target record.
     * Self-edit by non-global-admins is denied; admins and team admins editing
     * other users are allowed.
     */
    public static function canEditPrivilegedFields(?User $record): bool
    {
        /** @var User|null $authUser */
        $authUser = auth()->user();

        if (! $authUser) {
            return false;
        }

        if ($authUser->hasAnyAppRole([RoleEnum::SUPER_ADMIN, RoleEnum::ADMIN])) {
            return true;
        }

        // Self-edit by anyone other than a global admin: no privileged fields.
        if ($record !== null && $authUser->id === $record->id) {
            return false;
        }

        // Editing a different user — allow if the actor has any team-admin authority.
        return $authUser->hasAnyAppRole([RoleEnum::TEAM_ADMIN]);
    }

    /**
     * Returns true if any of the selected role IDs maps to a team-scoped role
     * (TEAM_ADMIN / COACH / ATHLETE). Used to toggle the Team select's visibility.
     *
     * @param  array<int|string>  $roleIds
     */
    public static function hasTeamScopedRole(array $roleIds): bool
    {
        if (empty($roleIds)) {
            return false;
        }

        $names = Role::query()->whereIn('id', $roleIds)->pluck('name');
        $teamScoped = array_map(fn (RoleEnum $r) => $r->value, RoleEnum::teamScopedCases());

        return $names->intersect($teamScoped)->isNotEmpty();
    }

    /**
     * Returns true if any of the selected role IDs maps to CUSTOMER or ATHLETE
     * — the only roles that pay membership fees. Used to gate the has_free_membership toggle.
     *
     * @param  array<int|string>  $roleIds
     */
    public static function hasMembershipEligibleRole(array $roleIds): bool
    {
        if (empty($roleIds)) {
            return false;
        }

        $names = Role::query()->whereIn('id', $roleIds)->pluck('name');

        return $names->intersect([RoleEnum::CUSTOMER->value, RoleEnum::ATHLETE->value])->isNotEmpty();
    }
}
