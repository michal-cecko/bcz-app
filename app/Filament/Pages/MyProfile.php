<?php

namespace App\Filament\Pages;

use App\Enums\DraftStatusEnum;
use App\Enums\GoalStatusEnum;
use App\Enums\ProfileTypeEnum;
use App\Filament\Pages\ProfileSchemas\AthleteProfileSchema;
use App\Filament\Pages\ProfileSchemas\CoachProfileSchema;
use App\Filament\Pages\ProfileSchemas\JudgeProfileSchema;
use App\Models\AthleteProfile;
use App\Models\CoachProfile;
use App\Models\Exercise;
use App\Models\JudgeProfile;
use App\Models\User;
use App\Services\ProfileDraftService;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Repeater\TableColumn;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class MyProfile extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedUserCircle;

    protected static ?string $navigationLabel = 'Verejny profil';

    protected static ?string $title = 'Verejny profil';

    protected static ?int $navigationSort = 6;

    public ?array $coachData = [];

    public ?array $athleteData = [];

    public ?array $judgeData = [];

    public ?array $certifications = [];

    public ?array $galleryCoach = [];

    public ?array $galleryAthlete = [];

    public ?array $galleryJudge = [];

    public ?array $exercises = [];

    public ?array $goals = [];

    public static function shouldRegisterNavigation(): bool
    {
        /** @var User|null $user */
        $user = auth()->user();

        return $user && count($user->getProfileableRoles()) > 0;
    }

    public function mount(): void
    {
        /** @var User $user */
        $user = auth()->user();
        $service = new ProfileDraftService;
        $roles = $user->getProfileableRoles();

        foreach ($roles as $type) {
            $profile = $this->getOrCreateProfile($user, $type);
            $data = $service->getDraftOrLiveData($profile);

            match ($type) {
                ProfileTypeEnum::Coach => $this->coachData = $data,
                ProfileTypeEnum::Athlete => $this->athleteData = $data,
                ProfileTypeEnum::Judge => $this->judgeData = $data,
            };
        }

        // Load certifications (shared, self-service)
        $this->certifications = $user->certifications()
            ->orderBy('sort_order')
            ->get()
            ->map(fn ($c) => [
                'id' => $c->id,
                'name_sk' => $c->getTranslation('name', 'sk'),
                'name_en' => $c->getTranslation('name', 'en'),
                'description_sk' => $c->getTranslation('description', 'sk'),
                'year_of_issue' => $c->year_of_issue,
            ])
            ->toArray();

        // Load gallery per role
        foreach ($roles as $type) {
            $items = $user->profileGalleryItems()
                ->where('profile_type', $type)
                ->orderBy('sort_order')
                ->get()
                ->map(fn ($g) => [
                    'id' => $g->id,
                    'description_sk' => $g->getTranslation('description', 'sk'),
                    'tags' => $g->tags ?? [],
                    'is_approved' => $g->is_approved,
                ])
                ->toArray();

            match ($type) {
                ProfileTypeEnum::Coach => $this->galleryCoach = $items,
                ProfileTypeEnum::Athlete => $this->galleryAthlete = $items,
                ProfileTypeEnum::Judge => $this->galleryJudge = $items,
            };
        }

        // Load athlete-specific data
        if (in_array(ProfileTypeEnum::Athlete, $roles)) {
            $this->exercises = $user->athleteExercises()
                ->orderBy('sort_order')
                ->get()
                ->map(fn ($e) => [
                    'id' => $e->id,
                    'exercise_id' => $e->exercise_id,
                    'duration' => $e->duration,
                    'description_sk' => $e->getTranslation('description', 'sk'),
                ])
                ->toArray();

            $this->goals = $user->athleteGoals()
                ->orderBy('sort_order')
                ->get()
                ->map(fn ($g) => [
                    'id' => $g->id,
                    'heading_sk' => $g->getTranslation('heading', 'sk'),
                    'description_sk' => $g->getTranslation('description', 'sk'),
                    'icon' => $g->icon,
                    'status' => $g->status?->value,
                ])
                ->toArray();
        }
    }

    public function content(Schema $schema): Schema
    {
        /** @var User $user */
        $user = auth()->user();
        $roles = $user->getProfileableRoles();

        $tabs = [];

        foreach ($roles as $type) {
            $tabs[] = match ($type) {
                ProfileTypeEnum::Coach => $this->buildCoachTab($user),
                ProfileTypeEnum::Athlete => $this->buildAthleteTab($user),
                ProfileTypeEnum::Judge => $this->buildJudgeTab($user),
            };
        }

        if (empty($tabs)) {
            return $schema->components([
                Placeholder::make('no_roles')
                    ->content('Nemate ziadnu rolu s verejnym profilom.'),
            ]);
        }

        return $schema->components([
            Tabs::make('profile_tabs')
                ->tabs($tabs)
                ->persistTabInQueryString(),
        ]);
    }

    public function saveCoachDraft(): void
    {
        $this->submitDraft(ProfileTypeEnum::Coach, $this->coachData);
    }

    public function saveAthleteDraft(): void
    {
        $this->submitDraft(ProfileTypeEnum::Athlete, $this->athleteData);
    }

    public function saveJudgeDraft(): void
    {
        $this->submitDraft(ProfileTypeEnum::Judge, $this->judgeData);
    }

    public function saveCertifications(): void
    {
        /** @var User $user */
        $user = auth()->user();

        // Delete existing and recreate
        $user->certifications()->delete();

        foreach ($this->certifications as $index => $cert) {
            if (empty($cert['name_sk'])) {
                continue;
            }

            $user->certifications()->create([
                'name' => ['sk' => $cert['name_sk'] ?? '', 'en' => $cert['name_en'] ?? ''],
                'description' => ['sk' => $cert['description_sk'] ?? ''],
                'year_of_issue' => $cert['year_of_issue'] ?? null,
                'sort_order' => $index,
            ]);
        }

        Notification::make()
            ->success()
            ->title('Certifikaty boli ulozene')
            ->send();
    }

    public function saveGallery(string $profileType): void
    {
        /** @var User $user */
        $user = auth()->user();
        $type = ProfileTypeEnum::from($profileType);

        $galleryData = match ($type) {
            ProfileTypeEnum::Coach => $this->galleryCoach,
            ProfileTypeEnum::Athlete => $this->galleryAthlete,
            ProfileTypeEnum::Judge => $this->galleryJudge,
        };

        // Delete existing items for this profile type and recreate
        $user->profileGalleryItems()->where('profile_type', $type)->delete();

        foreach ($galleryData as $index => $item) {
            $user->profileGalleryItems()->create([
                'profile_type' => $type,
                'description' => ['sk' => $item['description_sk'] ?? ''],
                'tags' => $item['tags'] ?? [],
                'sort_order' => $index,
                'is_approved' => false,
            ]);
        }

        Notification::make()
            ->success()
            ->title('Galeria bola ulozena (caka na schvalenie)')
            ->send();
    }

    protected function submitDraft(ProfileTypeEnum $type, array $data): void
    {
        /** @var User $user */
        $user = auth()->user();
        $profile = $this->getOrCreateProfile($user, $type);
        $service = new ProfileDraftService;

        $service->saveDraft($profile, $data);

        Notification::make()
            ->success()
            ->title('Profil bol odoslany na schvalenie')
            ->send();
    }

    protected function buildStatusBanner(User $user, ProfileTypeEnum $type): Placeholder
    {
        $profile = match ($type) {
            ProfileTypeEnum::Coach => $user->coachProfile,
            ProfileTypeEnum::Athlete => $user->athleteProfile,
            ProfileTypeEnum::Judge => $user->judgeProfile,
        };

        $isApproved = $user->isProfileApproved($type);
        $draftStatus = $profile?->draft_status;

        if ($draftStatus === DraftStatusEnum::Rejected) {
            $reason = $profile->draft_rejection_reason ?? '';

            return Placeholder::make("status_{$type->value}")
                ->label('')
                ->content("Zamietnuty: {$reason}")
                ->extraAttributes(['class' => 'text-danger-600 bg-danger-50 dark:bg-danger-950 dark:text-danger-400 p-3 rounded-lg']);
        }

        if ($draftStatus === DraftStatusEnum::Pending) {
            return Placeholder::make("status_{$type->value}")
                ->label('')
                ->content('Caka na schvalenie')
                ->extraAttributes(['class' => 'text-warning-600 bg-warning-50 dark:bg-warning-950 dark:text-warning-400 p-3 rounded-lg']);
        }

        if ($isApproved) {
            return Placeholder::make("status_{$type->value}")
                ->label('')
                ->content('Schvaleny')
                ->extraAttributes(['class' => 'text-success-600 bg-success-50 dark:bg-success-950 dark:text-success-400 p-3 rounded-lg']);
        }

        return Placeholder::make("status_{$type->value}")
            ->label('')
            ->content('Profil este nebol odoslany')
            ->extraAttributes(['class' => 'text-gray-500 bg-gray-50 dark:bg-gray-900 p-3 rounded-lg']);
    }

    protected function buildCoachTab(User $user): Tab
    {
        return Tab::make('Trener')
            ->icon(Heroicon::OutlinedAcademicCap)
            ->schema([
                $this->buildStatusBanner($user, ProfileTypeEnum::Coach),
                ...CoachProfileSchema::getFields('coachData'),
                $this->buildSubmitAction('saveCoachDraft'),
                $this->buildCertificationsSection(),
                $this->buildGallerySection('galleryCoach', ProfileTypeEnum::Coach),
            ]);
    }

    protected function buildAthleteTab(User $user): Tab
    {
        return Tab::make('Sportovec')
            ->icon(Heroicon::OutlinedTrophy)
            ->schema([
                $this->buildStatusBanner($user, ProfileTypeEnum::Athlete),
                ...AthleteProfileSchema::getFields('athleteData'),
                $this->buildExercisesSection(),
                $this->buildGoalsSection(),
                $this->buildSubmitAction('saveAthleteDraft'),
                $this->buildCertificationsSection(),
                $this->buildGallerySection('galleryAthlete', ProfileTypeEnum::Athlete),
            ]);
    }

    protected function buildJudgeTab(User $user): Tab
    {
        return Tab::make('Porotca')
            ->icon(Heroicon::OutlinedScale)
            ->schema([
                $this->buildStatusBanner($user, ProfileTypeEnum::Judge),
                ...JudgeProfileSchema::getFields('judgeData'),
                $this->buildSubmitAction('saveJudgeDraft'),
                $this->buildCertificationsSection(),
                $this->buildGallerySection('galleryJudge', ProfileTypeEnum::Judge),
            ]);
    }

    protected function buildCertificationsSection(): Section
    {
        return Section::make('Certifikaty a vzdelanie')
            ->icon(Heroicon::OutlinedAcademicCap)
            ->collapsed()
            ->schema([
                Repeater::make('certifications')
                    ->label('')
                    ->table([
                        TableColumn::make('Nazov (SK)'),
                        TableColumn::make('Rok'),
                    ])
                    ->schema([
                        TextInput::make('name_sk')
                            ->label('Nazov (SK)')
                            ->required(),
                        TextInput::make('name_en')
                            ->label('Name (EN)'),
                        Textarea::make('description_sk')
                            ->label('Popis (SK)')
                            ->rows(2),
                        TextInput::make('year_of_issue')
                            ->label('Rok vydania')
                            ->numeric()
                            ->minValue(1990)
                            ->maxValue(date('Y')),
                    ])
                    ->addActionLabel('Pridat certifikat')
                    ->reorderable()
                    ->defaultItems(0),
                $this->buildSaveAction('saveCertifications', 'Ulozit certifikaty'),
            ]);
    }

    protected function buildGallerySection(string $statePath, ProfileTypeEnum $type): Section
    {
        return Section::make('Galeria')
            ->icon(Heroicon::OutlinedPhoto)
            ->collapsed()
            ->description('Nove obrazky budu schvalene spolu s profilom')
            ->schema([
                Repeater::make($statePath)
                    ->label('')
                    ->table([
                        TableColumn::make('Popis'),
                        TableColumn::make('Tagy'),
                    ])
                    ->schema([
                        Textarea::make('description_sk')
                            ->label('Popis (SK)')
                            ->rows(2),
                        TagsInput::make('tags')
                            ->label('Tagy'),
                    ])
                    ->addActionLabel('Pridat obrazok')
                    ->reorderable()
                    ->defaultItems(0),
                $this->buildSaveAction("saveGallery('{$type->value}')", 'Ulozit galeriu'),
            ]);
    }

    protected function buildExercisesSection(): Section
    {
        return Section::make('Cesta k prvkom')
            ->icon(Heroicon::OutlinedBolt)
            ->collapsed()
            ->description('Prvky a cviky, ktore ste sa naucili')
            ->schema([
                Repeater::make('exercises')
                    ->label('')
                    ->table([
                        TableColumn::make('Cvik'),
                        TableColumn::make('Trvanie'),
                    ])
                    ->schema([
                        Select::make('exercise_id')
                            ->label('Cvik')
                            ->options(Exercise::query()->pluck('name', 'id')->map(fn ($name) => is_array($name) ? ($name['sk'] ?? '') : $name))
                            ->searchable(),
                        TextInput::make('duration')
                            ->label('Cas ucenia'),
                        Textarea::make('description_sk')
                            ->label('Popis (SK)')
                            ->rows(2),
                    ])
                    ->addActionLabel('Pridat prvok')
                    ->reorderable()
                    ->defaultItems(0),
            ]);
    }

    protected function buildGoalsSection(): Section
    {
        return Section::make('Moje ciele')
            ->icon(Heroicon::OutlinedFlag)
            ->collapsed()
            ->schema([
                Repeater::make('goals')
                    ->label('')
                    ->table([
                        TableColumn::make('Ciel'),
                        TableColumn::make('Status'),
                    ])
                    ->schema([
                        TextInput::make('heading_sk')
                            ->label('Nazov ciela (SK)')
                            ->required(),
                        Textarea::make('description_sk')
                            ->label('Popis (SK)')
                            ->rows(2),
                        TextInput::make('icon')
                            ->label('Ikona (lucide nazov)'),
                        Select::make('status')
                            ->label('Status')
                            ->options(GoalStatusEnum::translations())
                            ->default('planned'),
                    ])
                    ->addActionLabel('Pridat ciel')
                    ->reorderable()
                    ->defaultItems(0),
            ]);
    }

    protected function buildSaveAction(string $method, string $label): Placeholder
    {
        return Placeholder::make("save_{$method}")
            ->label('')
            ->content(
                view('filament.components.profile-submit-button', ['method' => $method, 'label' => $label])
            );
    }

    protected function buildSubmitAction(string $method): Placeholder
    {
        return Placeholder::make("submit_{$method}")
            ->label('')
            ->content(
                view('filament.components.profile-submit-button', ['method' => $method])
            );
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
