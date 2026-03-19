<?php

namespace App\Filament\Pages\Auth;

use App\Enums\GenderEnum;
use App\Enums\RoleEnum;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Pages\SimplePage;
use Filament\Schemas\Components\EmbeddedSchema;
use Filament\Schemas\Components\Form;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Wizard;
use Filament\Schemas\Components\Wizard\Step;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class SetPassword extends SimplePage
{
    protected static bool $shouldRegisterNavigation = false;

    public ?array $data = [];

    public function mount(): void
    {
        /** @var User|null $user */
        $user = auth()->user();

        if (! $user || $user->password_set_at !== null) {
            redirect('/admin');

            return;
        }

        $this->form->fill([
            'phone' => $user->phone,
            'birth_date' => $user->birth_date,
            'gender' => $user->gender?->value,
            'locale' => $user->locale ?? 'sk',
        ]);
    }

    public function getTitle(): string
    {
        return 'Vitajte v BCZ';
    }

    public function getHeading(): string
    {
        return 'Vitajte v BCZ';
    }

    public function getSubheading(): ?string
    {
        return 'Váš účet bol vytvorený pri registrácii. Dokončite nastavenie profilu.';
    }

    public function getMaxWidth(): string
    {
        return '2xl';
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
        $steps = [
            $this->getPasswordStep(),
            $this->getPersonalInfoStep(),
            $this->getProfileStep(),
        ];

        if ($this->shouldShowPublicProfileStep()) {
            $steps[] = $this->getPublicProfileStep();
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

                // Re-hash session so AuthenticateSession middleware doesn't log out
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

    protected function getPublicProfileStep(): Step
    {
        /** @var User $user */
        $user = auth()->user();
        $teamRole = $this->getUserPublicProfileRole();

        return Step::make('Verejný profil')
            ->description('Nastavte si verejný profil')
            ->icon('heroicon-o-globe-alt')
            ->schema([
                Toggle::make('has_public_profile')
                    ->label('Chcem mať verejný profil')
                    ->live(),

                ...$this->getPublicProfileFields($teamRole),

                Placeholder::make('approval_info')
                    ->content('Po odoslaní bude váš profil čakať na schválenie administrátorom.')
                    ->visible(fn (Get $get): bool => (bool) $get('has_public_profile')),
            ]);
    }

    /** @return list<Component> */
    protected function getPublicProfileFields(?string $teamRole): array
    {
        if ($teamRole === 'athlete') {
            return [
                DatePicker::make('date_started_working_out')
                    ->label('Začiatok cvičenia')
                    ->native(false)
                    ->maxDate(now())
                    ->visible(fn (Get $get): bool => (bool) $get('has_public_profile')),
                Textarea::make('journey_text')
                    ->label('Vaša cesta (SK)')
                    ->rows(4)
                    ->visible(fn (Get $get): bool => (bool) $get('has_public_profile')),
            ];
        }

        if ($teamRole === 'coach') {
            return [
                DatePicker::make('date_started_coaching')
                    ->label('Začiatok trénerskej kariéry')
                    ->native(false)
                    ->maxDate(now())
                    ->visible(fn (Get $get): bool => (bool) $get('has_public_profile')),
                Textarea::make('biography')
                    ->label('Biografia (SK)')
                    ->rows(4)
                    ->visible(fn (Get $get): bool => (bool) $get('has_public_profile')),
            ];
        }

        // Judge — no extra fields
        return [];
    }

    protected function shouldShowPublicProfileStep(): bool
    {
        return $this->getUserPublicProfileRole() !== null;
    }

    /**
     * Determine which public-profile-eligible role the user has.
     * Returns 'athlete', 'coach', or 'judge', or null.
     */
    protected function getUserPublicProfileRole(): ?string
    {
        /** @var User $user */
        $user = auth()->user();

        if ($user->hasRole(RoleEnum::JUDGE)) {
            return 'judge';
        }

        // Check team-scoped roles across all teams
        $hasCoach = $user->teams()
            ->wherePivot('role', RoleEnum::COACH->value)
            ->exists();

        if ($hasCoach) {
            return 'coach';
        }

        $hasAthlete = $user->teams()
            ->wherePivot('role', RoleEnum::ATHLETE->value)
            ->exists();

        if ($hasAthlete) {
            return 'athlete';
        }

        return null;
    }

    public function save(): void
    {
        $state = $this->form->getState();

        /** @var User $user */
        $user = auth()->user();

        // Save personal info (step 2)
        $user->update(array_filter([
            'phone' => $state['phone'] ?? null,
            'birth_date' => $state['birth_date'] ?? null,
            'gender' => $state['gender'] ?? null,
            'locale' => $state['locale'] ?? 'sk',
        ], fn ($value) => $value !== null));

        // Save public profile (step 4)
        if (! empty($state['has_public_profile'])) {
            $user->update([
                'has_public_profile' => true,
            ]);

            $teamRole = $this->getUserPublicProfileRole();

            if ($teamRole === 'athlete') {
                $user->athleteProfile()->updateOrCreate(
                    ['user_id' => $user->id],
                    array_filter([
                        'date_started_working_out' => $state['date_started_working_out'] ?? null,
                        'journey_text' => ! empty($state['journey_text'])
                            ? ['sk' => $state['journey_text']]
                            : null,
                    ], fn ($value) => $value !== null),
                );
            }

            if ($teamRole === 'coach') {
                $user->coachProfile()->updateOrCreate(
                    ['user_id' => $user->id],
                    array_filter([
                        'date_started_coaching' => $state['date_started_coaching'] ?? null,
                        'biography' => ! empty($state['biography'])
                            ? ['sk' => $state['biography']]
                            : null,
                    ], fn ($value) => $value !== null),
                );
            }
        }

        Notification::make()
            ->success()
            ->title('Profil bol úspešne nastavený')
            ->send();

        redirect('/admin');
    }
}
