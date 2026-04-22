<?php

namespace App\Filament\Pages\Auth;

use App\Enums\GenderEnum;
use App\Enums\ProfileTypeEnum;
use App\Enums\RoleEnum;
use App\Filament\Schemas\PublicProfileSchema;
use App\Models\AthleteProfile;
use App\Models\CoachProfile;
use App\Models\JudgeProfile;
use App\Models\User;
use App\Services\ProfileDraftService;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Pages\SimplePage;
use Filament\Schemas\Components\EmbeddedSchema;
use Filament\Schemas\Components\Form;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Wizard;
use Filament\Schemas\Components\Wizard\Step;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class UserSetupWizard extends SimplePage
{
    protected static bool $shouldRegisterNavigation = false;

    public ?array $data = [];

    public bool $isRevisit = false;

    public static function canAccess(): bool
    {
        /** @var User|null $user */
        $user = auth()->user();

        return $user !== null
            && ($user->hasRole([RoleEnum::CUSTOMER->value])
                || $user->hasRole([RoleEnum::ATHLETE->value]));
    }

    public function mount(): void
    {
        /** @var User|null $user */
        $user = auth()->user();

        if (! $user) {
            redirect('/admin');

            return;
        }

        $this->isRevisit = $user->password_set_at !== null;

        $this->form->fill([
            'phone' => $user->phone,
            'birth_date' => $user->birth_date,
            'gender' => $user->gender?->value,
            'locale' => $user->locale ?? 'sk',
        ]);
    }

    public function getTitle(): string
    {
        return $this->isRevisit ? 'Upraviť profil' : 'Vitajte v BCZ';
    }

    public function getHeading(): string
    {
        return $this->isRevisit ? 'Upraviť profil' : 'Vitajte v BCZ';
    }

    public function getSubheading(): ?string
    {
        return $this->isRevisit
            ? 'Aktualizujte vaše osobné údaje a verejný profil.'
            : 'Váš účet bol vytvorený pri registrácii. Dokončite nastavenie profilu.';
    }

    public function getMaxWidth(): string
    {
        return '5xl';
    }

    public function content(Schema $schema): Schema
    {
        return $schema
            ->components([
                Form::make([EmbeddedSchema::make('form')])
                    ->id('form')
                    ->livewireSubmitHandler('save'),
            ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->statePath('data')
            ->components([
                Wizard::make($this->getWizardSteps())
                    ->cancelAction(
                        Action::make('cancel')
                            ->label('Zrušiť')
                            ->url('/admin')
                            ->color('gray')
                    )
                    ->submitAction(
                        Action::make('save')
                            ->label('Dokončiť')
                            ->submit('form')
                    ),
            ]);
    }

    /** @return list<Step> */
    protected function getWizardSteps(): array
    {
        $steps = [];

        if (! $this->isRevisit) {
            $steps[] = $this->getPasswordStep();
        }

        $steps[] = $this->getPersonalInfoStep();
        $steps[] = $this->getProfileStep();

        /** @var User $user */
        $user = auth()->user();

        foreach ($user->getProfileableRoles() as $profileType) {
            $steps[] = $this->getPublicProfileStep($profileType);
        }

        return $steps;
    }

    protected function getPasswordStep(): Step
    {
        return Step::make('Heslo')
            ->description('Nastavte si heslo')
            ->icon('heroicon-o-lock-closed')
            ->schema([
                TextInput::make('password')
                    ->label('Nové heslo')
                    ->password()
                    ->revealable()
                    ->required()
                    ->rule(Password::default())
                    ->same('passwordConfirmation')
                    ->validationAttribute('heslo'),
                TextInput::make('passwordConfirmation')
                    ->label('Potvrďte heslo')
                    ->password()
                    ->revealable()
                    ->required()
                    ->dehydrated(false)
                    ->validationAttribute('potvrdenie hesla'),
            ])
            ->afterValidation(function () {
                /** @var User $user */
                $user = auth()->user();
                $state = $this->form->getState();

                $user->update([
                    'password' => Hash::make($state['password']),
                    'password_set_at' => now(),
                ]);

                session()->put([
                    'password_hash_'.auth()->getDefaultDriver() => $user->fresh()->getAuthPassword(),
                ]);
            });
    }

    protected function getPersonalInfoStep(): Step
    {
        return Step::make('Osobné údaje')
            ->description('Doplňte základné informácie')
            ->icon('heroicon-o-user')
            ->schema([
                TextInput::make('phone')
                    ->label('Telefón')
                    ->tel(),
                DatePicker::make('birth_date')
                    ->label('Dátum narodenia')
                    ->native(false)
                    ->maxDate(now()),
                Select::make('gender')
                    ->label('Pohlavie')
                    ->options(GenderEnum::translations()),
            ]);
    }

    protected function getProfileStep(): Step
    {
        return Step::make('Profil')
            ->description('Váš obrázok a jazyk')
            ->icon('heroicon-o-camera')
            ->schema([
                SpatieMediaLibraryFileUpload::make('profile_image')
                    ->collection('profile_image')
                    ->disk('public')
                    ->visibility('public')
                    ->label('Profilový obrázok')
                    ->avatar()
                    ->circleCropper()
                    ->model(auth()->user()),
                Select::make('locale')
                    ->label('Predvolený jazyk')
                    ->options([
                        'sk' => 'Slovenčina',
                        'en' => 'Angličtina',
                        'cs' => 'Čeština',
                    ])
                    ->default('sk'),
            ]);
    }

    protected function getPublicProfileStep(ProfileTypeEnum $profileType): Step
    {
        $roleKey = $profileType->value;
        $toggleKey = "has_public_profile_{$roleKey}";

        $label = match ($profileType) {
            ProfileTypeEnum::Coach => 'Profil trénera',
            ProfileTypeEnum::Athlete => 'Profil športovca',
            ProfileTypeEnum::Judge => 'Profil porotcu',
        };

        $description = match ($profileType) {
            ProfileTypeEnum::Coach => 'Nastavte si verejný profil trénera',
            ProfileTypeEnum::Athlete => 'Nastavte si verejný profil športovca',
            ProfileTypeEnum::Judge => 'Nastavte si verejný profil porotcu',
        };

        /** @var User $user */
        $user = auth()->user();
        $profile = $this->getOrCreateProfile($user, $profileType);

        // Build sub-tabs using shared schema (no ->relationship(), wizard saves manually)
        $subtabs = PublicProfileSchema::roleSubTabs($roleKey, $profile);

        return Step::make($label)
            ->description($description)
            ->icon('heroicon-o-globe-alt')
            ->schema([
                Toggle::make($toggleKey)
                    ->label('Chcem mať verejný profil')
                    ->live(),

                Tabs::make("{$roleKey}_wizard_subtabs")
                    ->visible(fn (Get $get): bool => (bool) $get($toggleKey))
                    ->tabs($subtabs)
                    ->persistTabInQueryString("{$roleKey}-wizard-tab"),

                Placeholder::make("approval_info_{$roleKey}")
                    ->label('')
                    ->content('Po odoslaní bude váš profil čakať na schválenie administrátorom.')
                    ->visible(fn (Get $get): bool => (bool) $get($toggleKey)),
            ]);
    }

    public function save(): void
    {
        $state = $this->form->getState();

        /** @var User $user */
        $user = auth()->user();

        $user->update(array_filter([
            'phone' => $state['phone'] ?? null,
            'birth_date' => $state['birth_date'] ?? null,
            'gender' => $state['gender'] ?? null,
            'locale' => $state['locale'] ?? 'sk',
        ], fn ($value) => $value !== null));

        // Save public profiles as drafts (wizard uses flat state, no ->relationship())
        $service = new ProfileDraftService;

        if (! empty($state['has_public_profile_coach'])) {
            $profile = $this->getOrCreateProfile($user, ProfileTypeEnum::Coach);
            $draftData = array_filter([
                'date_started_coaching' => $state['date_started_coaching'] ?? null,
                'biography' => $this->collectTranslations($state, 'biography'),
            ], fn ($value) => $value !== null);
            $service->saveDraft($profile, $draftData);
        }

        if (! empty($state['has_public_profile_athlete'])) {
            $profile = $this->getOrCreateProfile($user, ProfileTypeEnum::Athlete);
            $draftData = array_filter([
                'date_started_working_out' => $state['date_started_working_out'] ?? null,
                'journey_text' => $this->collectTranslations($state, 'journey_text'),
            ], fn ($value) => $value !== null);
            $service->saveDraft($profile, $draftData);
        }

        if (! empty($state['has_public_profile_judge'])) {
            $profile = $this->getOrCreateProfile($user, ProfileTypeEnum::Judge);
            $draftData = array_filter([
                'date_started_judging' => $state['date_started_judging'] ?? null,
                'disciplines' => $state['disciplines'] ?? null,
                'biography' => $this->collectTranslations($state, 'biography'),
            ], fn ($value) => $value !== null);
            $service->saveDraft($profile, $draftData);
        }

        Notification::make()
            ->success()
            ->title('Profil bol úspešne nastavený')
            ->send();

        redirect('/admin');
    }

    /**
     * Collect SK/EN/CS translations from nested dot-notation state into a translatable array.
     *
     * @return array<string, string>|null
     */
    protected function collectTranslations(array $state, string $field): ?array
    {
        $translations = array_filter([
            'sk' => $state[$field]['sk'] ?? null,
            'en' => $state[$field]['en'] ?? null,
            'cs' => $state[$field]['cs'] ?? null,
        ], fn ($value) => ! empty($value));

        return ! empty($translations) ? $translations : null;
    }

    protected function getOrCreateProfile(User $user, ProfileTypeEnum $type): CoachProfile|AthleteProfile|JudgeProfile
    {
        return match ($type) {
            ProfileTypeEnum::Coach => $user->coachProfile ?? $user->coachProfile()->create(['user_id' => $user->id]),
            ProfileTypeEnum::Athlete => $user->athleteProfile ?? $user->athleteProfile()->create(['user_id' => $user->id]),
            ProfileTypeEnum::Judge => $user->judgeProfile ?? $user->judgeProfile()->create(['user_id' => $user->id]),
        };
    }
}
