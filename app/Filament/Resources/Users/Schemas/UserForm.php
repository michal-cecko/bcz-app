<?php

namespace App\Filament\Resources\Users\Schemas;

use App\Enums\DraftStatusEnum;
use App\Enums\GenderEnum;
use App\Enums\RoleEnum;
use App\Filament\Schemas\PublicProfileSchema;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
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
                        self::passwordTab(),
                        self::publicProfilesTab(),
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
                                    ->required(),
                                Select::make('roles')
                                    ->label('Roly')
                                    ->relationship('roles', 'name')
                                    ->getOptionLabelFromRecordUsing(fn ($record): string => RoleEnum::tryFrom($record->name)?->getLabel() ?? $record->name)
                                    ->options(
                                        Role::query()
                                            ->whereNotIn('name', ['panel_user', RoleEnum::SUPER_ADMIN->value])
                                            ->pluck('name', 'id')
                                            ->map(fn (string $name): string => RoleEnum::tryFrom($name)?->getLabel() ?? $name)
                                    )
                                    ->multiple()
                                    ->preload()
                                    ->required(),
                                Select::make('gender')
                                    ->label('Pohlavie')
                                    ->options(GenderEnum::translations()),
                                DatePicker::make('birth_date')
                                    ->label('Dátum narodenia')
                                    ->native(false)
                                    ->maxDate(now()),
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
                            ->label('Heslo')
                            ->password()
                            ->revealable()
                            ->required(fn (string $operation): bool => $operation === 'create')
                            ->dehydrated(fn (?string $state): bool => filled($state))
                            ->same('passwordConfirmation'),
                        TextInput::make('passwordConfirmation')
                            ->label('Potvrdenie hesla')
                            ->password()
                            ->revealable()
                            ->dehydrated(false),
                    ]),
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
                        self::buildRoleTab('judge'),
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
            'judge' => [
                'label' => 'Porotca',
                'icon' => 'heroicon-o-scale',
                'approvalCol' => 'judge_profile_approved_at',
                'profileRel' => 'judgeProfile',
                'visibleFn' => fn ($record) => $record && $record->hasRole(RoleEnum::JUDGE),
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
            'judge' => ['approvalCol' => 'judge_profile_approved_at', 'relation' => 'judgeProfile', 'route' => 'judge.show'],
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
                                "<a href='{$url}' target='_blank' class='fi-btn fi-btn-size-sm fi-color-gray inline-flex items-center gap-1.5 rounded-lg px-3 py-2 text-sm font-semibold shadow-sm bg-white/5 text-white ring-1 ring-white/10 hover:bg-white/10 transition'>".
                                "<svg class='w-4 h-4' xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke-width='1.5' stroke='currentColor'><path stroke-linecap='round' stroke-linejoin='round' d='M13.5 6H5.25A2.25 2.25 0 0 0 3 8.25v10.5A2.25 2.25 0 0 0 5.25 21h10.5A2.25 2.25 0 0 0 18 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25' /></svg>".
                                'Zobraziť verejný profil</a>'
                            );
                        }),
                    Section::make()
                        ->relationship($profileConfig['relation'])
                        ->schema(PublicProfileSchema::profileFields($role)),
                ]),
        ];

        if (in_array($role, ['coach', 'judge'])) {
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

        $galleryRepeater = PublicProfileSchema::galleryRepeater($role)
            ->relationship()
            ->mutateRelationshipDataBeforeCreateUsing(function (array $data) use ($role): array {
                $data['profile_type'] = $role;

                return $data;
            });

        $tabs[] = Tab::make('Galéria')
            ->icon('heroicon-o-photo')
            ->schema([
                Section::make()
                    ->description('Nové obrázky budú schválené spolu s profilom')
                    ->schema([$galleryRepeater]),
            ]);

        return $tabs;
    }
}
