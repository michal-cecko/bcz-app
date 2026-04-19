<?php

namespace Database\Seeders;

use App\Enums\BannerTypeEnum;
use App\Enums\BillingPeriodEnum;
use App\Enums\CoachRoleEnum;
use App\Enums\ComplexityLevelEnum;
use App\Enums\GenderEnum;
use App\Enums\InquiryReasonEnum;
use App\Enums\InquiryStatusEnum;
use App\Enums\MembershipStatusEnum;
use App\Enums\PaymentMethodEnum;
use App\Enums\PaymentStatusEnum;
use App\Enums\PayoutStatusEnum;
use App\Enums\RegistrationStatusEnum;
use App\Enums\RoleEnum;
use App\Enums\RoundAdvancementTypeEnum;
use App\Enums\ScoringFormatEnum;
use App\Enums\SponsorTagEnum;
use App\Enums\SubscriptionStatusEnum;
use App\Enums\TimetableEntryStatusEnum;
use App\Enums\TimetableEntryTypeEnum;
use App\Enums\TrainingPricingTypeEnum;
use App\Models\AthleteCategory;
use App\Models\AthleteExercise;
use App\Models\AthleteGoal;
use App\Models\AthleteProfile;
use App\Models\Banner;
use App\Models\Battle;
use App\Models\BattleCompetitor;
use App\Models\Certification;
use App\Models\City;
use App\Models\CoachProfile;
use App\Models\CompetitionDetail;
use App\Models\CompetitionResult;
use App\Models\CompetitionRound;
use App\Models\Discipline;
use App\Models\Event;
use App\Models\EventCategory;
use App\Models\EventOrganization;
use App\Models\EventRegistration;
use App\Models\Exercise;
use App\Models\ExerciseCategory;
use App\Models\Faq;
use App\Models\FaqCategory;
use App\Models\Inquiry;
use App\Models\JudgeProfile;
use App\Models\Membership;
use App\Models\Payment;
use App\Models\RegistrationFee;
use App\Models\RoundPart;
use App\Models\Sponsor;
use App\Models\SportCategory;
use App\Models\SubscriptionPlan;
use App\Models\Team;
use App\Models\TeamPayout;
use App\Models\TeamSeason;
use App\Models\TeamSubscription;
use App\Models\TimetableEntry;
use App\Models\Training;
use App\Models\TrainingRegistration;
use App\Models\TrainingWaitlist;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DemoDataSeeder extends Seeder
{
    /**
     * Convert flat brick arrays to Mason's expected storage format.
     *
     * Flat: [['type' => 'rich-text', 'content' => ['sk' => '...']]]
     * Mason: [['type' => 'masonBrick', 'attrs' => ['id' => 'rich-text', 'config' => ['content' => ['sk' => '...']]]]]
     */
    private static function masonBricks(array $flatBricks): array
    {
        return array_map(function (array $brick) {
            $brickType = $brick['type'];
            unset($brick['type']);

            return [
                'type' => 'masonBrick',
                'attrs' => [
                    'id' => $brickType,
                    'config' => $brick,
                ],
            ];
        }, $flatBricks);
    }

    public function run(): void
    {
        $bczTeam = Team::query()->where('slug', 'bcz-club')->firstOrFail();
        $secondTeam = Team::factory()->create([
            'name' => ['sk' => 'Gravity Crew', 'en' => 'Gravity Crew', 'cz' => 'Gravity Crew'],
        ]);

        $sportCategories = SportCategory::all();
        $sportCategories->each(fn (SportCategory $cat) => $cat->update(['team_id' => $bczTeam->id]));
        $parkour = $sportCategories->firstWhere('slug', 'parkour-freerunning');
        $streetWorkout = $sportCategories->firstWhere('slug', 'street-workout');

        // --- Users with roles ---
        $coachData = [
            [
                'first_name' => 'Michal',
                'last_name' => 'Čečko',
                'email' => 'michal@bczclub.com',
                'biography' => [
                    'sk' => '8 rokov aktívneho tréningu a 5 rokov skúseností s vedením skupín. Michal sa špecializuje na výuku techniky a bezpečný progres. Jeho tréningy sú známe skvelou atmosférou a individuálnym prístupom ku každému účastníkovi.',
                    'en' => '8 years of active training and 5 years of experience leading groups. Michal specializes in technique instruction and safe progression. His trainings are known for their great atmosphere and individual approach to each participant.',
                ],
            ],
            [
                'first_name' => 'Dominik',
                'last_name' => 'Klimek',
                'email' => 'dominik@bczclub.com',
                'biography' => [
                    'sk' => 'Spoluzakladateľ BCZ Club a profesionálny parkour atléta s 10 rokmi skúseností. Dominik vedie pokročilé tréningy a pripravuje atlétov na súťaže. Je držiteľom certifikátu A.D.A.P.T. a pravidelne sa zúčastňuje medzinárodných workshopov.',
                    'en' => 'Co-founder of BCZ Club and professional parkour athlete with 10 years of experience. Dominik leads advanced trainings and prepares athletes for competitions. He holds the A.D.A.P.T. certificate and regularly participates in international workshops.',
                ],
            ],
            [
                'first_name' => 'Tomáš',
                'last_name' => 'Bartek',
                'email' => 'tomas@bczclub.com',
                'biography' => [
                    'sk' => 'Certifikovaný tréner kalisteniky a street workoutu. Tomáš má za sebou 6 rokov súťažného street workoutu a viacero umiestnení na slovenských a českých súťažiach. Zameriava sa na silový tréning s vlastnou váhou a progresiu k náročným prvkom.',
                    'en' => 'Certified calisthenics and street workout coach. Tomáš has 6 years of competitive street workout experience and multiple placements at Slovak and Czech competitions. He focuses on bodyweight strength training and progression to advanced elements.',
                ],
            ],
        ];

        $coaches = collect($coachData)->map(function ($data, $index) use ($bczTeam) {
            $user = User::factory()->create([
                'first_name' => $data['first_name'],
                'last_name' => $data['last_name'],
                'email' => $data['email'],
            ]);
            $user->assignRole(RoleEnum::CUSTOMER);
            $user->teams()->attach($bczTeam, ['role' => RoleEnum::COACH->value, 'is_active' => true, 'joined_at' => now()->subMonths(rand(6, 36))]);

            $coachProfile = CoachProfile::factory()->create([
                'user_id' => $user->id,
                'biography' => $data['biography'],
            ]);

            $coachProfile->addMediaFromUrl("https://picsum.photos/seed/coach-{$index}/400/500")
                ->usingFileName("coach-{$index}.jpg")
                ->toMediaCollection('biography_image');

            // Approve coach public profile
            $user->update([
                'coach_profile_approved_at' => now()->subDays(rand(1, 60)),
            ]);

            return $user;
        });

        $athletes = User::factory(8)->create()->each(function (User $user, int $index) use ($bczTeam) {
            $user->assignRole(RoleEnum::CUSTOMER);
            $user->teams()->attach($bczTeam, ['role' => RoleEnum::ATHLETE->value, 'is_active' => true, 'joined_at' => now()->subMonths(rand(1, 24))]);
            AthleteProfile::factory()->create(['user_id' => $user->id]);

            // Give first 5 athletes a public profile (approved) with photo
            if ($index < 5) {
                $user->update([
                    'athlete_profile_approved_at' => now()->subDays(rand(1, 60)),
                ]);
                $user->addMediaFromUrl("https://picsum.photos/seed/athlete-{$index}/400/500")
                    ->toMediaCollection('profile_image');
            }
        });

        $judgeData = [
            [
                'first_name' => 'Peter',
                'last_name' => 'Novák',
                'country_code' => 'SK',
                'biography' => ['sk' => 'Peter Novák je skúsený porotca so 7-ročnou praxou v hodnotení street workout a freestyle súťaží. Ako bývalý aktívny atlét rozumie technickej stránke disciplín a dokáže objektívne ohodnotiť výkony súťažiacich.', 'en' => 'Peter Novák is an experienced judge with 7 years of practice in evaluating street workout and freestyle competitions. As a former active athlete, he understands the technical aspects and can objectively evaluate performances.'],
                'disciplines' => ['freestyle', 'speed'],
                'date_started_judging' => '2019-03-15',
                'certifications' => [
                    ['name' => ['sk' => 'WSWCF Level A', 'en' => 'WSWCF Level A'], 'description' => ['sk' => 'Medzinárodná rozhodcovská licencia World Street Workout & Calisthenics Federation', 'en' => 'International judge license from World Street Workout & Calisthenics Federation'], 'year_of_issue' => 2021],
                    ['name' => ['sk' => 'Hlavný porotca SR', 'en' => 'Head Judge SK'], 'description' => ['sk' => 'Oprávnenie hlavného porotcu pre súťaže na Slovensku', 'en' => 'Head judge authorization for competitions in Slovakia'], 'year_of_issue' => 2023],
                ],
            ],
            [
                'first_name' => 'Tomáš',
                'last_name' => 'Horváth',
                'country_code' => 'SK',
                'biography' => ['sk' => 'Tomáš Horváth sa venuje hodnoteniu parkour a freestyle súťaží od roku 2020. Špecializuje sa na technickú analýzu a bezpečnostné aspekty výkonov.', 'en' => 'Tomáš Horváth has been judging parkour and freestyle competitions since 2020. He specializes in technical analysis and safety aspects of performances.'],
                'disciplines' => ['freestyle'],
                'date_started_judging' => '2020-06-01',
                'certifications' => [
                    ['name' => ['sk' => 'FIG Parkour Judge', 'en' => 'FIG Parkour Judge'], 'description' => ['sk' => 'Medzinárodná rozhodcovská licencia Fédération Internationale de Gymnastique', 'en' => 'International judge license from Fédération Internationale de Gymnastique'], 'year_of_issue' => 2022],
                ],
            ],
            [
                'first_name' => 'Marek',
                'last_name' => 'Kováč',
                'country_code' => 'CZ',
                'biography' => ['sk' => 'Marek Kováč je český porotca pôsobiaci na medzinárodných súťažiach. Má bohaté skúsenosti s hodnotením freestyle a endurance disciplín.', 'en' => 'Marek Kováč is a Czech judge active in international competitions. He has extensive experience in evaluating freestyle and endurance disciplines.'],
                'disciplines' => ['freestyle', 'endurance'],
                'date_started_judging' => '2021-01-10',
                'certifications' => [
                    ['name' => ['sk' => 'WSWCF Level B', 'en' => 'WSWCF Level B'], 'description' => ['sk' => 'Rozhodcovská licencia World Street Workout & Calisthenics Federation', 'en' => 'Judge license from World Street Workout & Calisthenics Federation'], 'year_of_issue' => 2023],
                    ['name' => ['sk' => 'Porotca Freestyle', 'en' => 'Freestyle Judge'], 'description' => ['sk' => 'Špecializácia na hodnotenie freestyle disciplín', 'en' => 'Specialization in freestyle discipline judging'], 'year_of_issue' => 2024],
                ],
            ],
            [
                'first_name' => 'Jakub',
                'last_name' => 'Vlček',
                'country_code' => 'SK',
                'biography' => ['sk' => 'Jakub Vlček je najmladší člen nášho rozhodcovského tímu. Prináša svieži pohľad na hodnotenie a aktívne sa vzdeláva v oblasti medzinárodných štandardov.', 'en' => 'Jakub Vlček is the youngest member of our judging team. He brings a fresh perspective to evaluation and actively educates himself in international standards.'],
                'disciplines' => ['strength'],
                'date_started_judging' => '2023-09-01',
                'certifications' => [
                    ['name' => ['sk' => 'BCZ Certified Judge', 'en' => 'BCZ Certified Judge'], 'description' => ['sk' => 'Interná rozhodcovská certifikácia BCZ Club', 'en' => 'Internal BCZ Club judge certification'], 'year_of_issue' => 2025],
                ],
            ],
        ];

        $judges = collect($judgeData)->map(function ($data) use ($bczTeam) {
            $user = User::factory()->create([
                'first_name' => $data['first_name'],
                'last_name' => $data['last_name'],
                'country_code' => $data['country_code'],
            ]);
            $user->assignRole(RoleEnum::JUDGE);
            $user->teams()->attach($bczTeam, ['role' => RoleEnum::ATHLETE->value, 'is_active' => true, 'joined_at' => now()->subMonths(rand(3, 12))]);

            JudgeProfile::create([
                'user_id' => $user->id,
                'biography' => $data['biography'],
                'disciplines' => $data['disciplines'],
                'date_started_judging' => $data['date_started_judging'],
            ]);

            // Approve judge public profile + add profile photo
            $user->update([
                'judge_profile_approved_at' => now()->subDays(rand(1, 60)),
            ]);
            $user->addMediaFromUrl('https://i.pravatar.cc/300?u='.$user->email)
                ->toMediaCollection('profile_image');

            foreach ($data['certifications'] as $index => $cert) {
                Certification::factory()->create([
                    'user_id' => $user->id,
                    'name' => $cert['name'],
                    'description' => $cert['description'],
                    'year_of_issue' => $cert['year_of_issue'],
                    'sort_order' => $index,
                ]);
            }

            return $user;
        });

        $members = User::factory(5)->create()->each(function (User $user) use ($bczTeam) {
            $user->assignRole(RoleEnum::CUSTOMER);
            $user->teams()->attach($bczTeam, ['role' => RoleEnum::ATHLETE->value, 'is_active' => true, 'joined_at' => now()->subMonths(rand(1, 6))]);
        });

        // --- Customer users (no team) ---
        User::factory(3)->create()->each(function (User $user) {
            $user->assignRole(RoleEnum::CUSTOMER);
        });

        // --- Athlete profile images ---
        $athletes->each(function (User $user, int $index) {
            $profile = $user->athleteProfile;
            if ($profile) {
                $profile->addMediaFromUrl("https://picsum.photos/seed/athlete-{$index}/400/500")
                    ->usingFileName("profile-{$index}.jpg")
                    ->toMediaCollection('main_image');
            }
        });

        // Upload team logo placeholder
        $bczTeam->addMediaFromUrl('https://picsum.photos/seed/bcz-logo/200/200')
            ->usingFileName('bcz-club-logo.png')
            ->toMediaCollection('logo');

        // --- Exercise Categories & Exercises ---
        $exerciseCategories = collect([
            ['name' => ['sk' => 'Ťahy', 'en' => 'Pull'], 'description' => ['sk' => 'Ťahové cviky (zhyby, šplh, ...)']],
            ['name' => ['sk' => 'Tlaky', 'en' => 'Push'], 'description' => ['sk' => 'Tlakové cviky (kliky, stojky, ...)']],
            ['name' => ['sk' => 'Nohy', 'en' => 'Legs'], 'description' => ['sk' => 'Cviky na nohy (drepy, výskoky, ...)']],
            ['name' => ['sk' => 'Jadro', 'en' => 'Core'], 'description' => ['sk' => 'Cviky na jadro tela (brušné svaly, ...)']],
        ])->map(function ($data, $index) use ($bczTeam, $parkour, $streetWorkout) {
            $cat = ExerciseCategory::factory()->create(array_merge($data, ['sort_order' => $index, 'team_id' => $bczTeam->id]));
            $cat->sportCategories()->attach($index < 2 ? $streetWorkout : $parkour);

            return $cat;
        });

        $exercises = collect();
        $exerciseNames = [
            ['name' => ['sk' => 'Precision Jump', 'en' => 'Precision Jump'], 'complexity' => ComplexityLevelEnum::BASIC],
            ['name' => ['sk' => 'Kong Vault', 'en' => 'Kong Vault'], 'complexity' => ComplexityLevelEnum::INTERMEDIATE],
            ['name' => ['sk' => 'Muscle Up', 'en' => 'Muscle Up'], 'complexity' => ComplexityLevelEnum::ADVANCED],
            ['name' => ['sk' => 'Planche', 'en' => 'Planche'], 'complexity' => ComplexityLevelEnum::ELITE],
            ['name' => ['sk' => 'Front Lever', 'en' => 'Front Lever'], 'complexity' => ComplexityLevelEnum::ADVANCED],
            ['name' => ['sk' => 'Wall Spin', 'en' => 'Wall Spin'], 'complexity' => ComplexityLevelEnum::INTERMEDIATE],
            ['name' => ['sk' => 'Backflip', 'en' => 'Backflip'], 'complexity' => ComplexityLevelEnum::ADVANCED],
            ['name' => ['sk' => 'Human Flag', 'en' => 'Human Flag'], 'complexity' => ComplexityLevelEnum::ELITE],
        ];

        foreach ($exerciseNames as $i => $data) {
            $exercises->push(Exercise::factory()->create(array_merge($data, [
                'exercise_category_id' => $exerciseCategories[$i % 4]->id,
                'team_id' => $bczTeam->id,
            ])));
        }

        // --- Athlete Exercises & Goals ---
        $athletes->each(function (User $athlete) use ($exercises) {
            $exercises->random(rand(2, 4))->each(function (Exercise $exercise, $index) use ($athlete) {
                AthleteExercise::factory()->create([
                    'user_id' => $athlete->id,
                    'exercise_id' => $exercise->id,
                    'sort_order' => $index,
                ]);
            });

            AthleteGoal::factory(rand(1, 3))->create(['user_id' => $athlete->id]);
            Certification::factory(rand(0, 2))->create(['user_id' => $athlete->id]);
        });

        // --- Registration form schemas for trainings ---
        $mandatoryFields = [
            ['label' => ['sk' => 'Meno', 'en' => 'First name', 'cs' => 'Jméno'], 'name' => 'meno', 'type' => 'first_name', 'width' => 'half', 'required' => true, 'has_condition' => false],
            ['label' => ['sk' => 'Priezvisko', 'en' => 'Last name', 'cs' => 'Příjmení'], 'name' => 'priezvisko', 'type' => 'last_name', 'width' => 'half', 'required' => true, 'has_condition' => false],
            ['label' => ['sk' => 'Email', 'en' => 'Email', 'cs' => 'Email'], 'name' => 'email', 'type' => 'email', 'width' => 'half', 'required' => true, 'placeholder' => ['sk' => 'tvoj@email.sk', 'en' => 'your@email.com', 'cs' => 'tvuj@email.cz'], 'has_condition' => false],
            ['label' => ['sk' => 'Telefón', 'en' => 'Phone', 'cs' => 'Telefon'], 'name' => 'telefon', 'type' => 'phone', 'width' => 'half', 'required' => true, 'placeholder' => ['sk' => '+421 XXX XXX XXX', 'en' => '+421 XXX XXX XXX', 'cs' => '+420 XXX XXX XXX'], 'has_condition' => false],
        ];

        $parentFields = [
            ['label' => ['sk' => 'Meno', 'en' => 'First name', 'cs' => 'Jméno'], 'name' => 'meno', 'type' => 'first_name', 'width' => 'half', 'required' => true, 'has_condition' => false],
            ['label' => ['sk' => 'Priezvisko', 'en' => 'Last name', 'cs' => 'Příjmení'], 'name' => 'priezvisko', 'type' => 'last_name', 'width' => 'half', 'required' => true, 'has_condition' => false],
            ['label' => ['sk' => 'Email', 'en' => 'Email', 'cs' => 'Email'], 'name' => 'email', 'type' => 'email', 'width' => 'half', 'required' => true, 'placeholder' => ['sk' => 'tvoj@email.sk', 'en' => 'your@email.com', 'cs' => 'tvuj@email.cz'], 'has_condition' => false],
            ['label' => ['sk' => 'Telefón', 'en' => 'Phone', 'cs' => 'Telefon'], 'name' => 'telefon', 'type' => 'phone', 'width' => 'half', 'required' => true, 'placeholder' => ['sk' => '+421 XXX XXX XXX', 'en' => '+421 XXX XXX XXX', 'cs' => '+420 XXX XXX XXX'], 'has_condition' => false],
            ['label' => ['sk' => 'Meno rodiča', 'en' => 'Parent name', 'cs' => 'Jméno rodiče'], 'name' => 'meno_rodica', 'type' => 'text_input', 'width' => 'half', 'required' => true, 'has_condition' => false],
            ['label' => ['sk' => 'Priezvisko rodiča', 'en' => 'Parent last name', 'cs' => 'Příjmení rodiče'], 'name' => 'priezvisko_rodica', 'type' => 'text_input', 'width' => 'half', 'required' => true, 'has_condition' => false],
            ['label' => ['sk' => 'Email rodiča', 'en' => 'Parent email', 'cs' => 'Email rodiče'], 'name' => 'email_rodica', 'type' => 'email', 'width' => 'full', 'required' => true, 'placeholder' => ['sk' => 'rodic@email.sk', 'en' => 'parent@email.com', 'cs' => 'rodic@email.cz'], 'has_condition' => false],
            ['label' => ['sk' => 'Meno dieťaťa', 'en' => 'Child name', 'cs' => 'Jméno dítěte'], 'name' => 'meno_dietata', 'type' => 'text_input', 'width' => 'half', 'required' => true, 'has_condition' => false],
            ['label' => ['sk' => 'Priezvisko dieťaťa', 'en' => 'Child last name', 'cs' => 'Příjmení dítěte'], 'name' => 'priezvisko_dietata', 'type' => 'text_input', 'width' => 'half', 'required' => true, 'has_condition' => false],
        ];

        $extraPhone = ['label' => ['sk' => 'Telefón', 'en' => 'Phone', 'cs' => 'Telefon'], 'name' => 'telefon', 'type' => 'phone', 'width' => 'full', 'required' => false, 'placeholder' => ['sk' => '+421 XXX XXX XXX', 'en' => '+421 XXX XXX XXX', 'cs' => '+420 XXX XXX XXX'], 'has_condition' => false];
        $extraAge = ['label' => ['sk' => 'Vek', 'en' => 'Age', 'cs' => 'Věk'], 'name' => 'vek', 'type' => 'number_input', 'width' => 'half', 'required' => true, 'has_condition' => false];
        $extraYear = ['label' => ['sk' => 'Rok narodenia', 'en' => 'Year of birth', 'cs' => 'Rok narození'], 'name' => 'rok_narodenia', 'type' => 'year_picker', 'width' => 'half', 'required' => true, 'has_condition' => false];
        $extraExperience = ['label' => ['sk' => 'Úroveň skúseností', 'en' => 'Experience level', 'cs' => 'Úroveň zkušeností'], 'name' => 'uroven_skusenosti', 'type' => 'select', 'width' => 'half', 'required' => true, 'options' => 'Začiatočník,Mierne pokročilý,Pokročilý', 'has_condition' => false];
        $extraNote = ['label' => ['sk' => 'Poznámka', 'en' => 'Note', 'cs' => 'Poznámka'], 'name' => 'poznamka', 'type' => 'textarea', 'width' => 'full', 'required' => false, 'placeholder' => ['sk' => 'Zdravotné obmedzenia, alergie...', 'en' => 'Health restrictions, allergies...', 'cs' => 'Zdravotní omezení, alergie...'], 'has_condition' => false];
        $extraTshirt = ['label' => ['sk' => 'Veľkosť trička', 'en' => 'T-shirt size', 'cs' => 'Velikost trička'], 'name' => 'velkost_tricka', 'type' => 'select', 'width' => 'half', 'required' => false, 'options' => 'XS,S,M,L,XL,XXL', 'has_condition' => false];
        $extraInsurance = ['label' => ['sk' => 'Máš poistenie?', 'en' => 'Do you have insurance?', 'cs' => 'Máš pojištění?'], 'name' => 'poistenie', 'type' => 'select', 'width' => 'half', 'required' => true, 'options' => 'Áno,Nie', 'has_condition' => false];
        $extraInsuranceDetail = ['label' => ['sk' => 'Číslo poistky', 'en' => 'Insurance number', 'cs' => 'Číslo pojistky'], 'name' => 'cislo_poistky', 'type' => 'text_input', 'width' => 'half', 'required' => true, 'has_condition' => true, 'condition_field' => 'poistenie', 'condition_value' => 'Áno'];

        $registrationSchemas = [
            // 0: Parkour Teens (kids → parent fields + age + note)
            array_merge($parentFields, [$extraAge, $extraNote]),
            // 1: Street Workout Advanced (standard + experience + note)
            array_merge($mandatoryFields, [$extraExperience, $extraNote]),
            // 2: Parkour pre pokročilých (standard + experience + year + insurance)
            array_merge($mandatoryFields, [$extraExperience, $extraYear, $extraInsurance, $extraInsuranceDetail]),
            // 3: Kalistenické základy (kids → parent fields + age)
            array_merge($parentFields, [$extraAge]),
            // 4: Street Workout pre deti (kids → parent fields + age + tshirt + note)
            array_merge($parentFields, [$extraAge, $extraTshirt, $extraNote]),
            // 5: Parkour & Freerunning Mix (standard + experience)
            array_merge($mandatoryFields, [$extraExperience]),
            // 6: Open Gym (standard only, minimal)
            $mandatoryFields,
            // 7: Tricking Workshop (standard + experience + year + tshirt + note)
            array_merge($mandatoryFields, [$extraYear, $extraExperience, $extraTshirt, $extraNote]),
            // 8: (if more trainings exist, fallback)
            array_merge($mandatoryFields, [$extraNote]),
        ];

        // --- Cities ---
        $banskaBystrica = City::create([
            'name' => ['sk' => 'Banská Bystrica', 'en' => 'Banská Bystrica', 'cs' => 'Banská Bystrica'],
            'sort_order' => 0,
        ]);
        $cadca = City::create([
            'name' => ['sk' => 'Čadca', 'en' => 'Čadca', 'cs' => 'Čadca'],
            'sort_order' => 1,
        ]);

        // --- Trainings ---
        $trainings = collect([
            [
                'title' => ['sk' => 'Parkour Teens', 'en' => 'Parkour Teens'],
                'description' => [
                    'sk' => "Parkour Teens je skupinový tréning určený pre mladých vo veku 13-17 rokov. Naučíš sa základy parkouru a freerunningU - od bezpečných pádov, cez preskoky a výstupy, až po dynamické pohyby a salto.\n\nTréningy sú zamerané na postupný progres, správnu techniku a hlavne zábavu v skvelej komunite.",
                    'en' => "Parkour Teens is a group training designed for youth aged 13-17. You will learn the basics of parkour and freerunning - from safe falls, to jumps and climbs, to dynamic movements and flips.\n\nTrainings focus on gradual progression, proper technique, and most importantly having fun in a great community.",
                ],
                'sport_category_id' => $parkour->id,
                'pricing_type' => TrainingPricingTypeEnum::MEMBERSHIP_REQUIRED,
                'min_age' => 13,
                'max_age' => 17,
                'max_capacity' => 12,
                '_schedules' => [['day' => 'monday', 'start_time' => '17:00'], ['day' => 'wednesday', 'start_time' => '17:00']],
                'duration_minutes' => 90,
                'place_name' => ['sk' => 'Športová hala Čadca', 'en' => 'Sports Hall Čadca'],
                'place_address' => 'Športovcov 1, 022 01 Čadca',
                'gathering_place' => ['sk' => 'Stretávame sa 10 minút pred začiatkom tréningu pri hlavnom vchode do športovej haly.', 'en' => 'We meet 10 minutes before the training at the main entrance of the sports hall.'],
                'latitude' => 49.4384,
                'longitude' => 18.7878,
            ],
            [
                'title' => ['sk' => 'Street Workout Advanced', 'en' => 'Street Workout Advanced'],
                'description' => [
                    'sk' => "Pokročilý tréning street workoutu pre skúsených atlétov. Zameriame sa na statické prvky ako planche, front lever a human flag, ale aj na dynamické kombinácie na hrazde.\n\nTréning vyžaduje zvládnutie základov kalisteniky - min. 10 čistých zhybov a 20 klikov.",
                    'en' => "Advanced street workout training for experienced athletes. We focus on static elements like planche, front lever and human flag, as well as dynamic combinations on the bar.\n\nTraining requires mastery of calisthenics basics - min. 10 clean pull-ups and 20 push-ups.",
                ],
                'sport_category_id' => $streetWorkout->id,
                'pricing_type' => TrainingPricingTypeEnum::MEMBERSHIP_REQUIRED,
                'min_age' => 16,
                'max_age' => null,
                'max_capacity' => 15,
                '_schedules' => [['day' => 'tuesday', 'start_time' => '18:00'], ['day' => 'thursday', 'start_time' => '18:00']],
                'duration_minutes' => 90,
                'place_name' => ['sk' => 'Workout Park Bratislava', 'en' => 'Workout Park Bratislava'],
                'place_address' => 'Tyršovo nábrežie, 851 01 Bratislava',
                'gathering_place' => ['sk' => 'Zraz pri hlavnej workout zóne na Tyršovom nábreží.', 'en' => 'Meeting at the main workout zone at Tyršovo nábrežie.'],
                'latitude' => 48.1389,
                'longitude' => 17.1051,
            ],
            [
                'title' => ['sk' => 'Freerunning Kreativita', 'en' => 'Freerunning Creativity'],
                'description' => [
                    'sk' => "Tréning zameraný na kreatívny pohyb a flow. Kombinujeme parkour, freerunning a tricking do plynulých zostáv. Dôraz kladieme na osobný štýl a originalitu.\n\nVhodné pre stredne pokročilých - požadujeme bezpečné zvládnutie základných preskokov a kotúľov.",
                    'en' => "Training focused on creative movement and flow. We combine parkour, freerunning and tricking into fluid routines. Emphasis on personal style and originality.\n\nSuitable for intermediate level - we require safe mastery of basic vaults and rolls.",
                ],
                'sport_category_id' => $parkour->id,
                'pricing_type' => TrainingPricingTypeEnum::MEMBERSHIP_REQUIRED,
                'min_age' => 14,
                'max_age' => 25,
                'max_capacity' => 20,
                '_schedules' => [['day' => 'wednesday', 'start_time' => '17:30'], ['day' => 'friday', 'start_time' => '17:30']],
                'duration_minutes' => 90,
                'place_name' => ['sk' => 'BCZ Gym Košice', 'en' => 'BCZ Gym Košice'],
                'place_address' => 'Hlavná 1, 040 01 Košice',
                'gathering_place' => ['sk' => 'Vstup cez zadný vchod zo strany parkoviska. Zvonček BCZ Gym.', 'en' => 'Entry through the back entrance from the parking side. BCZ Gym buzzer.'],
                'latitude' => 48.7164,
                'longitude' => 21.2611,
            ],
            [
                'title' => ['sk' => 'Parkour pre pokročilých', 'en' => 'Advanced Parkour'],
                'description' => [
                    'sk' => "Intenzívny tréning pre pokročilých parkouristov. Pracujeme na výškovej technike, presných doskokoch a efektívnom pohybe cez náročné prekážky v reálnom prostredí.\n\nPožadovaná úroveň: min. 2 roky pravidelného tréningu parkouru.",
                    'en' => "Intensive training for advanced traceurs. We work on height technique, precision landings and efficient movement through challenging obstacles in real environments.\n\nRequired level: min. 2 years of regular parkour training.",
                ],
                'sport_category_id' => $parkour->id,
                'pricing_type' => TrainingPricingTypeEnum::MEMBERSHIP_REQUIRED,
                'min_age' => 16,
                'max_age' => null,
                'max_capacity' => 10,
                '_schedules' => [['day' => 'saturday', 'start_time' => '10:00']],
                'duration_minutes' => 120,
                'place_name' => ['sk' => 'Outdoor spot Petržalka', 'en' => 'Outdoor spot Petržalka'],
                'place_address' => 'Námestie hraničiarov, 851 03 Bratislava',
                'gathering_place' => ['sk' => 'Zraz pri fontáne na Námestí hraničiarov. V prípade dažďa sa presúvame do krytej haly.', 'en' => 'Meeting at the fountain at Námestie hraničiarov. In case of rain we move to an indoor hall.'],
                'latitude' => 48.1228,
                'longitude' => 17.1100,
            ],
            [
                'title' => ['sk' => 'Kalistenické základy', 'en' => 'Calisthenics Basics'],
                'description' => [
                    'sk' => "Ideálny tréning pre úplných začiatočníkov. Naučíme ťa správnu techniku základných cvikov - zhyby, kliky, drepy, výdrže. Postupne budujeme silu a koordináciu pre náročnejšie prvky.\n\nŽiadne predchádzajúce skúsenosti nie sú potrebné!",
                    'en' => "Ideal training for complete beginners. We will teach you proper technique for basic exercises - pull-ups, push-ups, squats, holds. Gradually building strength and coordination for more advanced elements.\n\nNo prior experience needed!",
                ],
                'sport_category_id' => $streetWorkout->id,
                'pricing_type' => TrainingPricingTypeEnum::FREE,
                'min_age' => 10,
                'max_age' => 16,
                'max_capacity' => 20,
                '_schedules' => [['day' => 'monday', 'start_time' => '16:00'], ['day' => 'thursday', 'start_time' => '16:00']],
                'duration_minutes' => 60,
                'place_name' => ['sk' => 'Workout Park Čadca', 'en' => 'Workout Park Čadca'],
                'place_address' => 'Mestský park, 022 01 Čadca',
                'latitude' => 49.4405,
                'longitude' => 18.7863,
            ],
            [
                'title' => ['sk' => 'Street Workout pre deti', 'en' => 'Street Workout for Kids'],
                'description' => [
                    'sk' => "Zábavný tréning pre deti od 8 do 14 rokov. Formou hier a výziev sa naučia základy cvičenia s vlastnou váhou tela. Rozvíjame silu, ohybnosť a koordináciu v bezpečnom prostredí.\n\nKaždý tréning končí malou súťažou s odmenami!",
                    'en' => "Fun training for kids aged 8-14. Through games and challenges, they learn the basics of bodyweight exercises. We develop strength, flexibility and coordination in a safe environment.\n\nEvery training ends with a small competition with prizes!",
                ],
                'sport_category_id' => $streetWorkout->id,
                'pricing_type' => TrainingPricingTypeEnum::MEMBERSHIP_REQUIRED,
                'min_age' => 8,
                'max_age' => 14,
                'max_capacity' => 18,
                '_schedules' => [['day' => 'tuesday', 'start_time' => '15:30'], ['day' => 'friday', 'start_time' => '15:30']],
                'duration_minutes' => 60,
                'place_name' => ['sk' => 'Telocvičňa ZŠ Komenského', 'en' => 'Komenského Elementary School Gym'],
                'place_address' => 'Komenského 12, 022 01 Čadca',
                'gathering_place' => ['sk' => 'Vstup cez bočný vchod telocvične. Rodičia môžu počkať vo vestibule školy.', 'en' => 'Entry through the side entrance of the gym. Parents can wait in the school vestibule.'],
                'latitude' => 49.4362,
                'longitude' => 18.7921,
            ],
            [
                'title' => ['sk' => 'Parkour & Freerunning Mix', 'en' => 'Parkour & Freerunning Mix'],
                'description' => [
                    'sk' => "Kombinácia parkouru a freerunningU v jednom tréningu. Prvá polovica sa venuje efektívnemu pohybu a prekonávaniu prekážok, druhá akrobatickým prvkom a saltu.\n\nPre stredne pokročilých a pokročilých. Bezpečné zvládnutie kotúľov a preskokov je podmienkou.",
                    'en' => "Combination of parkour and freerunning in one training. First half focuses on efficient movement and obstacle negotiation, second half on acrobatic elements and flips.\n\nFor intermediate and advanced. Safe mastery of rolls and vaults is required.",
                ],
                'sport_category_id' => $parkour->id,
                'pricing_type' => TrainingPricingTypeEnum::MEMBERSHIP_REQUIRED,
                'min_age' => 14,
                'max_age' => 25,
                'max_capacity' => 16,
                '_schedules' => [['day' => 'monday', 'start_time' => '19:00'], ['day' => 'wednesday', 'start_time' => '19:00'], ['day' => 'friday', 'start_time' => '19:00']],
                'duration_minutes' => 90,
                'place_name' => ['sk' => 'BCZ Gym Bratislava', 'en' => 'BCZ Gym Bratislava'],
                'place_address' => 'Stará Vajnorská 37, 831 04 Bratislava',
                'gathering_place' => ['sk' => 'Zraz pri recepcii BCZ Gym. Šatne sú k dispozícii.', 'en' => 'Meeting at BCZ Gym reception. Changing rooms are available.'],
                'latitude' => 48.1698,
                'longitude' => 17.1436,
            ],
            [
                'title' => ['sk' => 'Open Gym', 'en' => 'Open Gym'],
                'description' => [
                    'sk' => "Otvorený tréning pre všetkých členov BCZ Club. Voľný prístup k celému vybaveniu - hrazdy, bradlá, trampolíny, mäkké dopadové plochy. Trénuj si čo chceš, tréner je k dispozícii pre radu.\n\nIdeálne na individuálny tréning a prácu na vlastných cieľoch.",
                    'en' => "Open training for all BCZ Club members. Free access to all equipment - bars, parallel bars, trampolines, soft landing areas. Train whatever you want, coach is available for advice.\n\nIdeal for individual training and working on personal goals.",
                ],
                'sport_category_id' => $streetWorkout->id,
                'pricing_type' => TrainingPricingTypeEnum::FREE,
                'min_age' => 16,
                'max_age' => null,
                'max_capacity' => 30,
                'notify_on_available' => true,
                '_schedules' => [['day' => 'saturday', 'start_time' => '09:00'], ['day' => 'sunday', 'start_time' => '09:00']],
                'duration_minutes' => 180,
                'place_name' => ['sk' => 'BCZ Gym Bratislava', 'en' => 'BCZ Gym Bratislava'],
                'place_address' => 'Stará Vajnorská 37, 831 04 Bratislava',
                'latitude' => 48.1698,
                'longitude' => 17.1436,
            ],
            [
                'title' => ['sk' => 'Tricking Workshop', 'en' => 'Tricking Workshop'],
                'description' => [
                    'sk' => "Intenzívny workshop zameraný na tricking - kombinácia akrobatických sált, kopov a tanečných pohybov. Pracujeme na technike, výške a rotáciách. Workshop je vedený hosťujúcim trénerom z Česka.\n\nLimitovaný počet miest - len 10 účastníkov pre maximálnu pozornosť trénera.",
                    'en' => "Intensive workshop focused on tricking - a combination of acrobatic flips, kicks and dance moves. We work on technique, height and rotations. Workshop is led by a guest coach from Czech Republic.\n\nLimited spots - only 10 participants for maximum coach attention.",
                ],
                'sport_category_id' => $parkour->id,
                'pricing_type' => TrainingPricingTypeEnum::PAID,
                'price_amount' => 25.00,
                'min_age' => 14,
                'max_age' => 25,
                'max_capacity' => 10,
                'is_recurring' => false,
                'event_date' => now()->addWeeks(3)->format('Y-m-d'),
                'notify_on_available' => true,
                'start_time' => '14:00',
                'duration_minutes' => 120,
                'place_name' => ['sk' => 'BCZ Gym Bratislava', 'en' => 'BCZ Gym Bratislava'],
                'place_address' => 'Stará Vajnorská 37, 831 04 Bratislava',
                'gathering_place' => ['sk' => 'Zraz pri recepcii BCZ Gym. Prineste si vlastné chrániče (voliteľné).', 'en' => 'Meeting at BCZ Gym reception. Bring your own protection gear (optional).'],
                'latitude' => 48.1698,
                'longitude' => 17.1436,
            ],
        ])->map(function ($data, $index) use ($bczTeam, $registrationSchemas, $banskaBystrica, $cadca) {
            // Čadca trainings: Parkour Teens (0), Kalistenické základy (4), Street Workout pre deti (5)
            // Banská Bystrica: Freerunning Kreativita (2), Parkour pre pokročilých (3)
            $cityMap = [0 => $cadca->id, 2 => $banskaBystrica->id, 3 => $banskaBystrica->id, 4 => $cadca->id, 5 => $cadca->id];

            $schedules = $data['_schedules'] ?? [];
            unset($data['_schedules']);

            $training = Training::factory()->create(array_merge($data, [
                'team_id' => $bczTeam->id,
                'city_id' => $cityMap[$index] ?? $cadca->id,
                'sort_order' => $index,
                'registration_form_schema' => $registrationSchemas[$index] ?? $registrationSchemas[count($registrationSchemas) - 1],
            ]));

            foreach ($schedules as $order => $schedule) {
                $training->schedules()->create(array_merge($schedule, ['sort_order' => $order]));
            }

            return $training;
        });

        // Attach coaches to trainings
        $trainings->each(function (Training $training, $index) use ($coaches) {
            $training->coaches()->attach($coaches[$index % 3]->id, [
                'role' => CoachRoleEnum::MAIN->value,
            ]);
            if ($index > 0) {
                $training->coaches()->attach($coaches[($index + 1) % 3]->id, [
                    'role' => CoachRoleEnum::SECONDARY->value,
                ]);
            }
        });

        // Training registrations — varied capacity and statuses
        $fillRatios = [0.0, 0.2, 0.5, 0.7, 0.75, 0.85, 0.92, 0.95, 1.0];
        $registrationStatuses = [
            RegistrationStatusEnum::Approved,
            RegistrationStatusEnum::Approved,
            RegistrationStatusEnum::Approved,
            RegistrationStatusEnum::Approved,
            RegistrationStatusEnum::Pending,
            RegistrationStatusEnum::Cancelled,
        ];
        $trainings->each(function (Training $training, int $index) use ($fillRatios, $registrationStatuses) {
            $ratio = $fillRatios[$index % count($fillRatios)];
            $registrationCount = (int) round($training->max_capacity * $ratio);

            for ($i = 0; $i < $registrationCount; $i++) {
                $status = $registrationStatuses[$i % count($registrationStatuses)];
                $cancellationReason = $status === RegistrationStatusEnum::Cancelled
                    ? fake()->randomElement([null, 'Zmena termínu', 'Osobné dôvody', 'Zdravotné problémy'])
                    : null;
                TrainingRegistration::factory()->forTraining($training)->create([
                    'user_id' => User::factory()->create()->id,
                    'status' => $status,
                    'cancellation_reason' => $cancellationReason,
                    'registered_at' => now()->subDays(rand(1, 60)),
                ]);
            }
        });

        // Waitlist entries — add users to trainings with notify_on_available enabled
        $trainings->filter(fn (Training $t) => $t->notify_on_available)
            ->each(function (Training $training) {
                $waitlistCount = rand(2, 5);
                for ($i = 0; $i < $waitlistCount; $i++) {
                    TrainingWaitlist::create([
                        'training_id' => $training->id,
                        'user_id' => User::factory()->create()->id,
                    ]);
                }
            });

        // --- Event Categories ---
        $eventCategories = collect([
            [
                'title' => ['sk' => 'Vystúpenia', 'en' => 'Exhibitions', 'cz' => 'Vystoupeni'],
                'color' => '#FF6B35',
                'card_subtitle' => ['sk' => 'Profesionálne parkour a kalisthenické vystúpenia'],
                'card_description' => ['sk' => 'Organizujeme vystúpenia na firemných akciách, festivaloch a súkromných podujatiach.'],
                'detail_title' => ['sk' => 'Naše vystúpenia'],
                'about_title' => ['sk' => 'O vystúpeniach'],
                'about_description' => ['sk' => 'Náš tím profesionálnych atlétov predvedie unikátne parkour a kalisthenické vystúpenia.'],
                'types_section_title' => ['sk' => 'Typy vystúpení'],
                'cta_title' => ['sk' => 'Chcete vystúpenie?'],
                'cta_description' => ['sk' => 'Kontaktujte nás pre nezáväznú ponuku.'],
                'types_cards' => [
                    ['title' => 'Indoor Show', 'description' => 'Vystúpenie v interiéri', 'icon' => 'heroicon-o-building-office'],
                    ['title' => 'Outdoor Show', 'description' => 'Vystúpenie v exteriéri', 'icon' => 'heroicon-o-sun'],
                ],
                'stats' => [
                    ['number' => '200+', 'label' => 'Vystúpení', 'icon' => 'heroicon-o-star'],
                    ['number' => '50+', 'label' => 'Klientov', 'icon' => 'heroicon-o-users'],
                ],
                'sort_order' => 1,
            ],
            [
                'title' => ['sk' => 'Prednášky', 'en' => 'Lectures', 'cz' => 'Prednasky'],
                'color' => '#2EC4B6',
                'card_subtitle' => ['sk' => 'Motivačné a vzdelávacie prednášky'],
                'card_description' => ['sk' => 'Prednášky o disciplíne, zdravom životnom štýle a prekonávaní prekážok.'],
                'sort_order' => 2,
            ],
            [
                'title' => ['sk' => 'Workshopy', 'en' => 'Workshops', 'cz' => 'Workshopy'],
                'color' => '#9B5DE5',
                'card_subtitle' => ['sk' => 'Praktické workshopy pre všetky úrovne'],
                'card_description' => ['sk' => 'Hands-on workshopy zamerané na základy parkour a kalistheniky.'],
                'sort_order' => 3,
            ],
            [
                'title' => ['sk' => 'Freestyle súťaž', 'en' => 'Freestyle Competition', 'cz' => 'Freestyle soutěž'],
                'color' => '#FF6B6B',
                'card_subtitle' => ['sk' => 'Freestyle súťaže pre všetky úrovne'],
                'card_description' => ['sk' => 'Súťaže vo freestyle kalistenike a street workoutových disciplínach.'],
                'sort_order' => 4,
            ],
        ])->map(function ($data) {
            return EventCategory::factory()->create(array_merge($data, ['is_active' => true]));
        });

        // --- Events (diverse scenarios) ---

        // === REPORT events — "Where we were" portfolio items ===

        // 1. Past exhibition at corporate event
        $tedxEvent = Event::factory()->create([
            'event_type' => 'report',
            'event_category_id' => $eventCategories[0]->id, // Vystupenia
            'team_id' => $bczTeam->id,
            'title' => ['sk' => 'Parkour Show na TEDx Bratislava', 'en' => 'Parkour Show at TEDx Bratislava', 'cs' => 'Parkour Show na TEDx Bratislava'],
            'card_description' => ['sk' => 'Dynamicke vystupenie nasich atletov na TEDx Bratislava 2025. Prepojenie parkouru s pribehovym vystupenim o prekonavani prekazok.', 'en' => 'Dynamic performance by our athletes at TEDx Bratislava 2025. Combining parkour with a narrative performance about overcoming obstacles.'],
            'date' => now()->subMonths(6),
            'country' => 'Slovensko',
            'city' => 'Bratislava',
            'attendee_count' => 800,
            'client' => 'TEDx Bratislava',
            'is_published' => true,
            'published_at' => now()->subMonths(6)->addDays(3),
            'content' => [
                'sk' => self::masonBricks([
                    ['type' => 'heading', 'level' => 'h2', 'text' => ['sk' => 'O vystúpení']],
                    ['type' => 'rich-text', 'content' => ['sk' => '<p>Naše vystúpenie na konferencii TEDx Bratislava 2025 bolo jedným z vrcholov programu. Pred publikom 800 divákov sme predviedli 15-minútovú choreografiu prepájajúcu parkour s príbehovým vystúpením o prekonávaní prekážok — fyzických aj mentálnych.</p><p>Spolupráca s organizátormi TEDx bola inšpiratívna. Celý koncept sme navrhli tak, aby korešpondoval s témou konferencie "Breaking Barriers". Atléti BCZ Club predviedli synchronizované sekvencie, ktoré symbolizovali rôzne životné výzvy.</p>']],
                    ['type' => 'heading', 'level' => 'h3', 'text' => ['sk' => 'Čo bolo súčasťou vystúpenia']],
                    ['type' => 'rich-text', 'content' => ['sk' => '<ul><li>Príbehová parkour choreografia (4 atléti)</li><li>Synchronizované precision jumpy</li><li>Solo tricking performance s narrative voiceoverom</li><li>Záverečná akrobatická pyramída</li></ul>']],
                    ['type' => 'quote', 'quote' => ['sk' => '<p>Vystúpenie BCZ Club bolo najsilnejším vizuálnym momentom celej konferencie. Dokonale prepojili fyzický výkon s myšlienkou prekonávania bariér.</p>'], 'attribution' => ['sk' => '— Mgr. Jana Kováčová, organizátorka TEDx Bratislava']],
                    ['type' => 'heading', 'level' => 'h3', 'text' => ['sk' => 'Technické parametre']],
                    ['type' => 'table', 'headers' => [['label' => ['sk' => 'Parameter']], ['label' => ['sk' => 'Hodnota']]], 'rows' => [
                        ['cells' => [['value' => ['sk' => 'Trvanie show']], ['value' => ['sk' => '15 minút']]]],
                        ['cells' => [['value' => ['sk' => 'Počet účinkujúcich']], ['value' => ['sk' => '4 atléti']]]],
                        ['cells' => [['value' => ['sk' => 'Typ']], ['value' => ['sk' => 'Indoor / Konferenčná scéna']]]],
                        ['cells' => [['value' => ['sk' => 'Priestor']], ['value' => ['sk' => 'Stará tržnica, Bratislava']]]],
                    ]],
                ]),
                'en' => self::masonBricks([
                    ['type' => 'heading', 'level' => 'h2', 'text' => ['en' => 'About the Show']],
                    ['type' => 'rich-text', 'content' => ['en' => '<p>Our performance at TEDx Bratislava 2025 was one of the highlights of the program. In front of 800 spectators, we delivered a 15-minute choreography combining parkour with a narrative performance about overcoming obstacles — both physical and mental.</p>']],
                    ['type' => 'heading', 'level' => 'h3', 'text' => ['en' => 'What Was Part of the Show']],
                    ['type' => 'rich-text', 'content' => ['en' => '<ul><li>Narrative parkour choreography (4 athletes)</li><li>Synchronized precision jumps</li><li>Solo tricking performance with narrative voiceover</li><li>Final acrobatic pyramid</li></ul>']],
                    ['type' => 'quote', 'quote' => ['en' => '<p>The BCZ Club performance was the strongest visual moment of the entire conference. They perfectly connected physical performance with the idea of breaking barriers.</p>'], 'attribution' => ['en' => '— Mgr. Jana Kováčová, TEDx Bratislava organizer']],
                    ['type' => 'heading', 'level' => 'h3', 'text' => ['en' => 'Technical Parameters']],
                    ['type' => 'table', 'headers' => [['label' => ['en' => 'Parameter']], ['label' => ['en' => 'Value']]], 'rows' => [
                        ['cells' => [['value' => ['en' => 'Show duration']], ['value' => ['en' => '15 minutes']]]],
                        ['cells' => [['value' => ['en' => 'Performers']], ['value' => ['en' => '4 athletes']]]],
                        ['cells' => [['value' => ['en' => 'Type']], ['value' => ['en' => 'Indoor / Conference stage']]]],
                        ['cells' => [['value' => ['en' => 'Venue']], ['value' => ['en' => 'Stará tržnica, Bratislava']]]],
                    ]],
                ]),
            ],
        ]);
        $tedxEvent->addMediaFromUrl('https://picsum.photos/seed/event-tedx-card/800/450')
            ->toMediaCollection('card_image');
        $tedxEvent->addMediaFromUrl('https://picsum.photos/seed/event-tedx-hero/1440/500')
            ->toMediaCollection('detail_image');

        // 2. Past corporate show
        $redbullEvent = Event::factory()->create([
            'event_type' => 'report',
            'event_category_id' => $eventCategories[0]->id, // Vystupenia
            'team_id' => $bczTeam->id,
            'title' => ['sk' => 'Red Bull Firemny Teambuilding', 'en' => 'Red Bull Corporate Teambuilding', 'cs' => 'Red Bull Firemni Teambuilding'],
            'card_description' => ['sk' => 'Privatne vystupenie a workshop pre zamestnancov Red Bull. 3-hodinovy program s interaktivnou castou.', 'en' => 'Private performance and workshop for Red Bull employees. 3-hour program with an interactive section.'],
            'date' => now()->subMonths(4),
            'country' => 'Slovensko',
            'city' => 'Bratislava',
            'attendee_count' => 120,
            'client' => 'Red Bull Slovakia',
            'is_published' => true,
            'published_at' => now()->subMonths(4)->addDays(5),
            'content' => [
                'sk' => self::masonBricks([
                    ['type' => 'heading', 'level' => 'h2', 'text' => ['sk' => 'O vystúpení']],
                    ['type' => 'rich-text', 'content' => ['sk' => '<p>Pre spoločnosť Red Bull Slovakia sme pripravili exkluzívny firemný teambuilding kombinujúci vystúpenie našich top atlétov s interaktívnym workshopom pre zamestnancov. Celý program trval 3 hodiny a bol rozdelený do troch blokov.</p><p>Prvá časť bola ukážková — naši atléti predviedli parkour a tricking performance. Druhá časť bola interaktívna — zamestnanci si pod vedením trénerov vyskúšali základné parkourové techniky. Tretia časť bola teamová výzva s prvkami spolupráce.</p>']],
                    ['type' => 'heading', 'level' => 'h3', 'text' => ['sk' => 'Čo bolo súčasťou programu']],
                    ['type' => 'rich-text', 'content' => ['sk' => '<ul><li>20-minútová parkour a tricking show</li><li>Interaktívny workshop základov pohybu (90 min)</li><li>Tímová výzva — parkourová štafeta (45 min)</li><li>Záverečný debrief a spoločné foto</li></ul>']],
                    ['type' => 'quote', 'quote' => ['sk' => '<p>Najlepší teambuilding, aký sme kedy mali. Zamestnanci boli nadšení a ešte týždne po evente sa o tom rozprávali. BCZ Club dodal profesionálny a zároveň zábavný program.</p>'], 'attribution' => ['sk' => '— Ing. Martin Horváth, HR Manager, Red Bull Slovakia']],
                    ['type' => 'heading', 'level' => 'h3', 'text' => ['sk' => 'Technické parametre']],
                    ['type' => 'table', 'headers' => [['label' => ['sk' => 'Parameter']], ['label' => ['sk' => 'Hodnota']]], 'rows' => [
                        ['cells' => [['value' => ['sk' => 'Trvanie programu']], ['value' => ['sk' => '3 hodiny']]]],
                        ['cells' => [['value' => ['sk' => 'Počet účinkujúcich']], ['value' => ['sk' => '6 atlétov + 2 tréneri']]]],
                        ['cells' => [['value' => ['sk' => 'Typ']], ['value' => ['sk' => 'Indoor / Firemný event']]]],
                        ['cells' => [['value' => ['sk' => 'Priestor']], ['value' => ['sk' => 'Red Bull HQ, Bratislava']]]],
                    ]],
                ]),
                'en' => self::masonBricks([
                    ['type' => 'heading', 'level' => 'h2', 'text' => ['en' => 'About the Show']],
                    ['type' => 'rich-text', 'content' => ['en' => '<p>We prepared an exclusive corporate teambuilding for Red Bull Slovakia combining a performance by our top athletes with an interactive workshop for employees. The 3-hour program was divided into three blocks: a showcase performance, hands-on training, and a team challenge.</p>']],
                    ['type' => 'heading', 'level' => 'h3', 'text' => ['en' => 'Program Highlights']],
                    ['type' => 'rich-text', 'content' => ['en' => '<ul><li>20-minute parkour and tricking show</li><li>Interactive movement basics workshop (90 min)</li><li>Team challenge — parkour relay (45 min)</li><li>Final debrief and group photo</li></ul>']],
                    ['type' => 'quote', 'quote' => ['en' => '<p>The best teambuilding we ever had. Employees were thrilled and talked about it for weeks. BCZ Club delivered a professional yet fun program.</p>'], 'attribution' => ['en' => '— Ing. Martin Horváth, HR Manager, Red Bull Slovakia']],
                    ['type' => 'heading', 'level' => 'h3', 'text' => ['en' => 'Technical Parameters']],
                    ['type' => 'table', 'headers' => [['label' => ['en' => 'Parameter']], ['label' => ['en' => 'Value']]], 'rows' => [
                        ['cells' => [['value' => ['en' => 'Program duration']], ['value' => ['en' => '3 hours']]]],
                        ['cells' => [['value' => ['en' => 'Performers']], ['value' => ['en' => '6 athletes + 2 coaches']]]],
                        ['cells' => [['value' => ['en' => 'Type']], ['value' => ['en' => 'Indoor / Corporate event']]]],
                        ['cells' => [['value' => ['en' => 'Venue']], ['value' => ['en' => 'Red Bull HQ, Bratislava']]]],
                    ]],
                ]),
            ],
        ]);
        $redbullEvent->addMediaFromUrl('https://picsum.photos/seed/event-redbull-card/800/450')
            ->toMediaCollection('card_image');
        $redbullEvent->addMediaFromUrl('https://picsum.photos/seed/event-redbull-hero/1440/500')
            ->toMediaCollection('detail_image');

        // 3. Past lecture
        $prednaskaEvent = Event::factory()->create([
            'event_type' => 'report',
            'event_category_id' => $eventCategories[1]->id, // Prednasky
            'team_id' => $bczTeam->id,
            'title' => ['sk' => 'Motivacna prednaska na Gymnazium Grösslingova', 'en' => 'Motivational Talk at Grösslingova High School', 'cs' => 'Motivacni prednaska na Gymnazium Grösslingova'],
            'card_description' => ['sk' => 'Prednaska o discipline, cielevedomosti a zdravom zivotnom style pre stredoskolakov.', 'en' => 'Talk about discipline, determination and healthy lifestyle for high school students.'],
            'date' => now()->subMonths(2),
            'country' => 'Slovensko',
            'city' => 'Bratislava',
            'attendee_count' => 250,
            'client' => 'Gymnazium Grösslingova',
            'is_published' => true,
            'published_at' => now()->subMonths(2)->addDay(),
            'content' => [
                'sk' => self::masonBricks([
                    ['type' => 'heading', 'level' => 'h2', 'text' => ['sk' => 'O prednáške']],
                    ['type' => 'rich-text', 'content' => ['sk' => '<p>Na Gymnáziu Grösslingová sme pre 250 študentov pripravili motivačnú prednášku o disciplíne, cieľavedomosti a zdravom životnom štýle. Prednáška bola interaktívna — kombinovali sme osobné príbehy našich atlétov s praktickými ukážkami.</p><p>Študenti mali možnosť vidieť, ako vyzerá cesta od úplného začiatočníka po profesionálneho atléta. Zdôraznili sme dôležitosť konzistentnosti, trpezlivosti a správneho okolia.</p>']],
                    ['type' => 'heading', 'level' => 'h3', 'text' => ['sk' => 'Témy prednášky']],
                    ['type' => 'rich-text', 'content' => ['sk' => '<ul><li>Disciplína ako základ úspechu — nielen v športe</li><li>Ako si vybudovať zdravé návyky od mladého veku</li><li>Prekonávanie strachu a vystúpenie z komfortnej zóny</li><li>Praktické ukážky parkourových techník</li><li>Q&A s atlétmi BCZ Club</li></ul>']],
                    ['type' => 'quote', 'quote' => ['sk' => '<p>Prednáška BCZ Club zaujala aj tých študentov, ktorí bežne na prednáškach nespolupracujú. Autentické príbehy a živé ukážky urobili obrovský dojem.</p>'], 'attribution' => ['sk' => '— PhDr. Eva Šimková, riaditeľka Gymnázia Grösslingová']],
                    ['type' => 'heading', 'level' => 'h3', 'text' => ['sk' => 'Technické parametre']],
                    ['type' => 'table', 'headers' => [['label' => ['sk' => 'Parameter']], ['label' => ['sk' => 'Hodnota']]], 'rows' => [
                        ['cells' => [['value' => ['sk' => 'Trvanie prednášky']], ['value' => ['sk' => '90 minút']]]],
                        ['cells' => [['value' => ['sk' => 'Počet prednášajúcich']], ['value' => ['sk' => '3 atléti']]]],
                        ['cells' => [['value' => ['sk' => 'Typ']], ['value' => ['sk' => 'Motivačná prednáška + ukážky']]]],
                        ['cells' => [['value' => ['sk' => 'Priestor']], ['value' => ['sk' => 'Aula Gymnázia Grösslingová, Bratislava']]]],
                    ]],
                ]),
                'en' => self::masonBricks([
                    ['type' => 'heading', 'level' => 'h2', 'text' => ['en' => 'About the Talk']],
                    ['type' => 'rich-text', 'content' => ['en' => '<p>At Grösslingová High School, we delivered a motivational talk for 250 students about discipline, determination, and healthy lifestyle. The talk was interactive — combining personal stories from our athletes with practical demonstrations.</p>']],
                    ['type' => 'heading', 'level' => 'h3', 'text' => ['en' => 'Talk Topics']],
                    ['type' => 'rich-text', 'content' => ['en' => '<ul><li>Discipline as the foundation of success — not just in sports</li><li>Building healthy habits from a young age</li><li>Overcoming fear and stepping out of your comfort zone</li><li>Practical parkour technique demonstrations</li><li>Q&A with BCZ Club athletes</li></ul>']],
                    ['type' => 'quote', 'quote' => ['en' => '<p>The BCZ Club talk engaged even those students who usually don\'t participate in lectures. Authentic stories and live demonstrations made a huge impression.</p>'], 'attribution' => ['en' => '— PhDr. Eva Šimková, Principal of Grösslingová High School']],
                    ['type' => 'heading', 'level' => 'h3', 'text' => ['en' => 'Technical Parameters']],
                    ['type' => 'table', 'headers' => [['label' => ['en' => 'Parameter']], ['label' => ['en' => 'Value']]], 'rows' => [
                        ['cells' => [['value' => ['en' => 'Talk duration']], ['value' => ['en' => '90 minutes']]]],
                        ['cells' => [['value' => ['en' => 'Speakers']], ['value' => ['en' => '3 athletes']]]],
                        ['cells' => [['value' => ['en' => 'Type']], ['value' => ['en' => 'Motivational talk + demonstrations']]]],
                        ['cells' => [['value' => ['en' => 'Venue']], ['value' => ['en' => 'Grösslingová High School Auditorium, Bratislava']]]],
                    ]],
                ]),
            ],
        ]);
        $prednaskaEvent->addMediaFromUrl('https://picsum.photos/seed/event-prednaska-card/800/450')
            ->toMediaCollection('card_image');
        $prednaskaEvent->addMediaFromUrl('https://picsum.photos/seed/event-prednaska-hero/1440/500')
            ->toMediaCollection('detail_image');

        // 4. Past international workshop report
        $prahaEvent = Event::factory()->create([
            'event_type' => 'report',
            'event_category_id' => $eventCategories[2]->id, // Workshopy
            'team_id' => $bczTeam->id,
            'title' => ['sk' => 'Medzinarodny Parkour Workshop Praha', 'en' => 'International Parkour Workshop Prague', 'cs' => 'Mezinarodni Parkour Workshop Praha'],
            'card_description' => ['sk' => 'Spolocny workshop s ceskou komunitou. 2-dnovka plna treningov, prednasok a networkingu.', 'en' => '2-day workshop with the Czech parkour community. Training, talks, and networking.'],
            'date' => now()->subMonths(8),
            'date_end' => now()->subMonths(8)->addDay(),
            'country' => 'Cesko',
            'city' => 'Praha',
            'attendee_count' => 60,
            'is_published' => true,
            'published_at' => now()->subMonths(8)->addDays(5),
            'content' => [
                'sk' => self::masonBricks([
                    ['type' => 'heading', 'level' => 'h2', 'text' => ['sk' => 'O workshope']],
                    ['type' => 'rich-text', 'content' => ['sk' => '<p>Spoločný medzinárodný workshop s českou parkour komunitou v Prahe bol dvojdňovým maratónom tréningov, prednášok a networkingu. Zúčastnilo sa 60 atlétov zo Slovenska, Česka a Poľska.</p><p>Workshop viedli skúsení tréneri z BCZ Club a českého PK Letná. Program bol rozdelený do levelov — od začiatočníkov po pokročilých. Každý si našiel niečo pre seba.</p>']],
                    ['type' => 'heading', 'level' => 'h3', 'text' => ['sk' => 'Program workshopu']],
                    ['type' => 'rich-text', 'content' => ['sk' => '<ul><li>Deň 1: Parkour fundamentals a flow tréning (4 hodiny)</li><li>Deň 1: Večerná prednáška o bezpečnosti a progresii</li><li>Deň 2: Tricking a akrobacia workshop (4 hodiny)</li><li>Deň 2: Spoločný jam a voľný tréning</li><li>Networking a plánovanie budúcej spolupráce</li></ul>']],
                    ['type' => 'quote', 'quote' => ['sk' => '<p>Spolupráca so slovenským BCZ Club bola fantastická. Ich prístup k tréningu a profesionalita sú na vysokej úrovni. Tešíme sa na ďalšie spoločné akcie.</p>'], 'attribution' => ['sk' => '— Tomáš Dvořák, vedúci tréner PK Letná, Praha']],
                    ['type' => 'heading', 'level' => 'h3', 'text' => ['sk' => 'Technické parametre']],
                    ['type' => 'table', 'headers' => [['label' => ['sk' => 'Parameter']], ['label' => ['sk' => 'Hodnota']]], 'rows' => [
                        ['cells' => [['value' => ['sk' => 'Trvanie']], ['value' => ['sk' => '2 dni']]]],
                        ['cells' => [['value' => ['sk' => 'Počet trénerov']], ['value' => ['sk' => '8 (4 SK + 4 CZ)']]]],
                        ['cells' => [['value' => ['sk' => 'Typ']], ['value' => ['sk' => 'Indoor + Outdoor workshop']]]],
                        ['cells' => [['value' => ['sk' => 'Priestor']], ['value' => ['sk' => 'PK Letná Gym + Letná Park, Praha']]]],
                    ]],
                ]),
                'en' => self::masonBricks([
                    ['type' => 'heading', 'level' => 'h2', 'text' => ['en' => 'About the Workshop']],
                    ['type' => 'rich-text', 'content' => ['en' => '<p>A joint international workshop with the Czech parkour community in Prague was a two-day marathon of training, talks, and networking. 60 athletes from Slovakia, Czech Republic, and Poland participated.</p>']],
                    ['type' => 'heading', 'level' => 'h3', 'text' => ['en' => 'Workshop Program']],
                    ['type' => 'rich-text', 'content' => ['en' => '<ul><li>Day 1: Parkour fundamentals and flow training (4 hours)</li><li>Day 1: Evening talk on safety and progression</li><li>Day 2: Tricking and acrobatics workshop (4 hours)</li><li>Day 2: Community jam and free training</li><li>Networking and future collaboration planning</li></ul>']],
                    ['type' => 'quote', 'quote' => ['en' => '<p>The collaboration with Slovak BCZ Club was fantastic. Their approach to training and professionalism are top-notch. We look forward to more joint events.</p>'], 'attribution' => ['en' => '— Tomáš Dvořák, Head Coach PK Letná, Prague']],
                    ['type' => 'heading', 'level' => 'h3', 'text' => ['en' => 'Technical Parameters']],
                    ['type' => 'table', 'headers' => [['label' => ['en' => 'Parameter']], ['label' => ['en' => 'Value']]], 'rows' => [
                        ['cells' => [['value' => ['en' => 'Duration']], ['value' => ['en' => '2 days']]]],
                        ['cells' => [['value' => ['en' => 'Coaches']], ['value' => ['en' => '8 (4 SK + 4 CZ)']]]],
                        ['cells' => [['value' => ['en' => 'Type']], ['value' => ['en' => 'Indoor + Outdoor workshop']]]],
                        ['cells' => [['value' => ['en' => 'Venue']], ['value' => ['en' => 'PK Letná Gym + Letná Park, Prague']]]],
                    ]],
                ]),
            ],
        ]);
        $prahaEvent->addMediaFromUrl('https://picsum.photos/seed/event-praha-card/800/450')
            ->toMediaCollection('card_image');
        $prahaEvent->addMediaFromUrl('https://picsum.photos/seed/event-praha-hero/1440/500')
            ->toMediaCollection('detail_image');

        // 5. Recent report — outdoor showcase
        $kosiceEvent = Event::factory()->create([
            'event_type' => 'report',
            'event_category_id' => $eventCategories[0]->id,
            'team_id' => $bczTeam->id,
            'title' => ['sk' => 'Street Show Kosice - Den Mesta', 'en' => 'Street Show Kosice - City Day', 'cs' => 'Street Show Kosice - Den Mesta'],
            'card_description' => ['sk' => 'Velke outdoor vystupenie na Hlavnej ulici pocas Dna mesta Kosice. Parkour, kalistenika, tricking.', 'en' => 'Large outdoor performance on Main Street during Kosice City Day. Parkour, calisthenics, tricking.'],
            'date' => now()->subWeeks(3),
            'country' => 'Slovensko',
            'city' => 'Kosice',
            'attendee_count' => 2000,
            'client' => 'Mesto Kosice',
            'is_published' => true,
            'published_at' => now()->subWeeks(2),
            'content' => [
                'sk' => self::masonBricks([
                    ['type' => 'heading', 'level' => 'h2', 'text' => ['sk' => 'O vystúpení']],
                    ['type' => 'rich-text', 'content' => ['sk' => '<p>Naše vystúpenie na Hlavnej ulici v Košiciach počas Dňa mesta bolo jedným z najväčších podujatí sezóny. Pred viac ako 2000 divákmi sme predviedli 30-minútovú show kombinujúcu parkour, kalisteniku a tricking.</p><p>Spolupráca s mestom Košice bola bezproblémová. Tím BCZ pripravil choreografiu špeciálne pre tento outdoorový event s využitím mestského mobiliáru — lavičiek, múrov a zábradlí.</p>']],
                    ['type' => 'heading', 'level' => 'h3', 'text' => ['sk' => 'Čo bolo súčasťou vystúpenia']],
                    ['type' => 'rich-text', 'content' => ['sk' => '<ul><li>Synchronizovaná parkour choreografia (5 atlétov)</li><li>Solo tricking performance</li><li>Interaktívna časť s publikom</li><li>Wall-flipy a precision jumps na mestskom mobiliári</li></ul>']],
                    ['type' => 'quote', 'quote' => ['sk' => '<p>BCZ Club priniesol na Deň mesta Košice niečo úplne nové. Energia a profesionalita ich vystúpenia nadchla celé publikum od detí po seniorov.</p>'], 'attribution' => ['sk' => '— Ing. Peter Novák, vedúci oddelenia kultúry mesta Košice']],
                    ['type' => 'heading', 'level' => 'h3', 'text' => ['sk' => 'Technické parametre']],
                    ['type' => 'table', 'headers' => [['label' => ['sk' => 'Parameter']], ['label' => ['sk' => 'Hodnota']]], 'rows' => [
                        ['cells' => [['value' => ['sk' => 'Trvanie show']], ['value' => ['sk' => '30 minút']]]],
                        ['cells' => [['value' => ['sk' => 'Počet účinkujúcich']], ['value' => ['sk' => '5 atlétov']]]],
                        ['cells' => [['value' => ['sk' => 'Typ']], ['value' => ['sk' => 'Outdoor / Street']]]],
                        ['cells' => [['value' => ['sk' => 'Priestor']], ['value' => ['sk' => 'Hlavná ulica, Košice']]]],
                    ]],
                ]),
                'en' => self::masonBricks([
                    ['type' => 'heading', 'level' => 'h2', 'text' => ['en' => 'About the Show']],
                    ['type' => 'rich-text', 'content' => ['en' => '<p>Our performance on Main Street in Košice during City Day was one of the biggest events of the season. In front of over 2,000 spectators, we delivered a 30-minute show combining parkour, calisthenics, and tricking using urban furniture.</p>']],
                    ['type' => 'heading', 'level' => 'h3', 'text' => ['en' => 'What Was Part of the Show']],
                    ['type' => 'rich-text', 'content' => ['en' => '<ul><li>Synchronized parkour choreography (5 athletes)</li><li>Solo tricking performance</li><li>Interactive audience section</li><li>Wall-flips and precision jumps on urban furniture</li></ul>']],
                    ['type' => 'quote', 'quote' => ['en' => '<p>BCZ Club brought something completely new to Košice City Day. The energy and professionalism of their performance thrilled the entire audience.</p>'], 'attribution' => ['en' => '— Ing. Peter Novák, Head of Culture Department, City of Košice']],
                    ['type' => 'heading', 'level' => 'h3', 'text' => ['en' => 'Technical Parameters']],
                    ['type' => 'table', 'headers' => [['label' => ['en' => 'Parameter']], ['label' => ['en' => 'Value']]], 'rows' => [
                        ['cells' => [['value' => ['en' => 'Show duration']], ['value' => ['en' => '30 minutes']]]],
                        ['cells' => [['value' => ['en' => 'Performers']], ['value' => ['en' => '5 athletes']]]],
                        ['cells' => [['value' => ['en' => 'Type']], ['value' => ['en' => 'Outdoor / Street']]]],
                        ['cells' => [['value' => ['en' => 'Venue']], ['value' => ['en' => 'Main Street, Košice']]]],
                    ]],
                ]),
            ],
        ]);
        $kosiceEvent->addMediaFromUrl('https://picsum.photos/seed/event-kosice-card/800/450')
            ->toMediaCollection('card_image');
        $kosiceEvent->addMediaFromUrl('https://picsum.photos/seed/event-kosice-hero/1440/500')
            ->toMediaCollection('detail_image');

        // === ORGANIZED events — camps, workshops, public trainings with registration ===

        // 6. Past organized — free community workshop (finished, full capacity)
        $pastWorkshop = Event::factory()->organized()->create([
            'event_category_id' => $eventCategories[2]->id, // Workshopy
            'team_id' => $bczTeam->id,
            'title' => ['sk' => 'Zakladny Parkour Workshop pre Deti', 'en' => 'Basic Parkour Workshop for Kids', 'cs' => 'Zakladni Parkour Workshop pro Deti'],
            'card_description' => ['sk' => 'Bezplatny workshop pre deti 8-14 rokov. Zaklady bezpecneho pohybu, padov a preskokov.', 'en' => 'Free workshop for kids aged 8-14. Basics of safe movement, falls and vaults.'],
            'date' => now()->subMonth(),
            'country' => 'Slovensko',
            'city' => 'Bratislava',
            'place_name' => 'Sportova hala Pasienky',
            'place_address' => 'Junacka 6, 831 04 Bratislava',
            'is_published' => true,
            'published_at' => now()->subMonths(3),
            'content' => [
                'sk' => self::masonBricks([
                    ['type' => 'heading', 'level' => 'h2', 'text' => ['sk' => 'O podujatí']],
                    ['type' => 'rich-text', 'content' => ['sk' => '<p>Bezplatný parkour workshop pre deti vo veku 8-14 rokov. Pod vedením certifikovaných trénerov BCZ Club sa deti naučia základy bezpečného pohybu, správne techniky pádov a jednoduché preskoky.</p><p>Workshop je vhodný pre úplných začiatočníkov. Všetky aktivity sú prispôsobené veku a schopnostiam detí. Bezpečnosť je naša priorita — používame žinenky a postupujeme od najjednoduchších cvičení.</p>']],
                    ['type' => 'heading', 'level' => 'h3', 'text' => ['sk' => 'Čo ťa čaká']],
                    ['type' => 'rich-text', 'content' => ['sk' => '<ul><li>Zahrievanie a príprava tela formou hier (20 min)</li><li>Základy bezpečných pádov — roll forward, roll backward (30 min)</li><li>Jednoduché preskoky a prekonávanie prekážok (30 min)</li><li>Mini parkour dráha a záverečná hra (20 min)</li><li>Spoločné foto a odovzdanie diplomov</li></ul>']],
                    ['type' => 'quote', 'quote' => ['sk' => '<p>Syn sa z workshopu vrátil nadšený a hneď chcel chodiť na pravidelné tréningy. Tréneri boli úžasní — trpezliví, profesionálni a deti sa s nimi cítili bezpečne.</p>'], 'attribution' => ['sk' => '— Katarína M., mama účastníka']],
                ]),
                'en' => self::masonBricks([
                    ['type' => 'heading', 'level' => 'h2', 'text' => ['en' => 'About the Event']],
                    ['type' => 'rich-text', 'content' => ['en' => '<p>Free parkour workshop for kids aged 8-14. Under the guidance of certified BCZ Club coaches, children learn the basics of safe movement, proper falling techniques, and simple vaults. Suitable for complete beginners.</p>']],
                    ['type' => 'heading', 'level' => 'h3', 'text' => ['en' => 'What to Expect']],
                    ['type' => 'rich-text', 'content' => ['en' => '<ul><li>Warm-up through games (20 min)</li><li>Safe falling basics — forward and backward rolls (30 min)</li><li>Simple vaults and obstacle navigation (30 min)</li><li>Mini parkour course and final game (20 min)</li><li>Group photo and certificate handout</li></ul>']],
                    ['type' => 'quote', 'quote' => ['en' => '<p>My son came back from the workshop thrilled and immediately wanted to attend regular training. The coaches were amazing — patient, professional, and the kids felt safe.</p>'], 'attribution' => ['en' => '— Katarína M., participant\'s mother']],
                ]),
            ],
        ]);
        $pastWorkshop->addMediaFromUrl('https://picsum.photos/seed/event-deti-card/800/450')
            ->toMediaCollection('card_image');
        $pastWorkshop->addMediaFromUrl('https://picsum.photos/seed/event-deti-hero/1440/500')
            ->toMediaCollection('detail_image');
        EventOrganization::factory()->create([
            'event_id' => $pastWorkshop->id,
            'max_capacity' => 30,
            'pricing_type' => 'free',
            'registration_opens_at' => now()->subMonths(3),
            'registration_closes_at' => now()->subMonth()->subWeek(),
            'is_public_registration' => true,
        ]);
        // Fill with 30 registrations (full)
        for ($i = 0; $i < 30; $i++) {
            EventRegistration::factory()->create([
                'event_id' => $pastWorkshop->id,
                'user_id' => User::factory()->create()->id,
                'status' => 'approved',
                'registered_at' => now()->subMonths(2)->addDays($i),
            ]);
        }

        // 7. Upcoming organized — paid summer camp (registration open, half full)
        $summerCamp = Event::factory()->organized()->create([
            'event_category_id' => $eventCategories[2]->id,
            'team_id' => $bczTeam->id,
            'title' => ['sk' => 'BCZ Letny Tabor 2026', 'en' => 'BCZ Summer Camp 2026', 'cs' => 'BCZ Letni Tabor 2026'],
            'card_description' => ['sk' => '5-dnovy letny tabor plny parkouru, kalisteniky, her a zabavy. Pre vsetky urovne od 12 rokov.', 'en' => '5-day summer camp full of parkour, calisthenics, games and fun. All levels from age 12.'],
            'date' => now()->addMonths(3),
            'date_end' => now()->addMonths(3)->addDays(5),
            'country' => 'Slovensko',
            'city' => 'Cadca',
            'place_name' => 'Sportovy areal Cadca',
            'place_address' => 'Sportova 15, 022 01 Cadca',
            'latitude' => 49.4384,
            'longitude' => 18.7878,
            'is_published' => true,
            'published_at' => now()->subWeeks(2),
            'content' => [
                'sk' => self::masonBricks([
                    ['type' => 'heading', 'level' => 'h2', 'text' => ['sk' => 'O podujatí']],
                    ['type' => 'rich-text', 'content' => ['sk' => '<p>BCZ Letný tábor 2026 je 5-dňový pobytový tábor plný parkouru, kalisteniky, hier a zábavy. Tábor je určený pre všetky úrovne od 12 rokov — či už si úplný začiatočník alebo pokročilý atléta.</p><p>Každý deň je rozdelený do blokov — ranný tréning, poobedné workshopy a večerné aktivity. Tréneri BCZ Club pripravia program prispôsobený jednotlivým skupinám podľa úrovne skúseností.</p>']],
                    ['type' => 'heading', 'level' => 'h3', 'text' => ['sk' => 'Program']],
                    ['type' => 'rich-text', 'content' => ['sk' => '<ul><li>Deň 1: Príchod, rozdelenie do skupín, úvodný tréning</li><li>Deň 2: Parkour fundamentals + kalistenika workshop</li><li>Deň 3: Tricking a akrobacia + výlet do prírody</li><li>Deň 4: Freestyle session + videoshoot</li><li>Deň 5: Záverečná show pre rodičov, vyhodnotenie, odchod</li><li>Večerné aktivity: filmový večer, táborák, nočná hra</li></ul>']],
                    ['type' => 'quote', 'quote' => ['sk' => '<p>Minuloročný tábor bol najlepší zážitok leta. Naučil som sa veci, o ktorých som si myslel, že nikdy nezvládnem. A kamaráti, ktorých som tam získal, sú najlepší.</p>'], 'attribution' => ['sk' => '— Jakub, 14 rokov, účastník BCZ Letného tábora 2025']],
                ]),
                'en' => self::masonBricks([
                    ['type' => 'heading', 'level' => 'h2', 'text' => ['en' => 'About the Event']],
                    ['type' => 'rich-text', 'content' => ['en' => '<p>BCZ Summer Camp 2026 is a 5-day residential camp full of parkour, calisthenics, games, and fun. The camp is designed for all levels from age 12 — whether you are a complete beginner or an advanced athlete.</p>']],
                    ['type' => 'heading', 'level' => 'h3', 'text' => ['en' => 'Program']],
                    ['type' => 'rich-text', 'content' => ['en' => '<ul><li>Day 1: Arrival, group assignment, introductory training</li><li>Day 2: Parkour fundamentals + calisthenics workshop</li><li>Day 3: Tricking and acrobatics + nature trip</li><li>Day 4: Freestyle session + video shoot</li><li>Day 5: Final show for parents, awards, departure</li></ul>']],
                    ['type' => 'quote', 'quote' => ['en' => '<p>Last year\'s camp was the best experience of the summer. I learned things I thought I\'d never manage. And the friends I made there are the best.</p>'], 'attribution' => ['en' => '— Jakub, 14 years old, BCZ Summer Camp 2025 participant']],
                ]),
            ],
        ]);
        $summerCamp->addMediaFromUrl('https://picsum.photos/seed/event-tabor-card/800/450')
            ->toMediaCollection('card_image');
        $summerCamp->addMediaFromUrl('https://picsum.photos/seed/event-tabor-hero/1440/500')
            ->toMediaCollection('detail_image');
        EventOrganization::factory()->paid(89.00)->create([
            'event_id' => $summerCamp->id,
            'max_capacity' => 40,
            'registration_opens_at' => now()->subWeeks(2),
            'registration_closes_at' => now()->addMonths(2),
            'is_public_registration' => true,
            'show_countdown' => true,
        ]);
        // 18 of 40 registered
        for ($i = 0; $i < 18; $i++) {
            EventRegistration::factory()->create([
                'event_id' => $summerCamp->id,
                'user_id' => User::factory()->create()->id,
                'status' => $i < 15 ? 'approved' : 'pending',
                'registered_at' => now()->subDays(14 - $i),
            ]);
        }

        // 8. Upcoming organized — free public training (registration opens soon)
        $publicTraining = Event::factory()->organized()->create([
            'event_category_id' => $eventCategories[2]->id,
            'team_id' => $bczTeam->id,
            'title' => ['sk' => 'Verejny Trening v Parku - Bratislava', 'en' => 'Public Training in the Park - Bratislava', 'cs' => 'Verejny Trenink v Parku - Bratislava'],
            'card_description' => ['sk' => 'Bezplatny otvoreny trening pre verejnost. Pride ktokolavek, naucime ta zaklady!', 'en' => 'Free open training for the public. Anyone can come, we will teach you the basics!'],
            'date' => now()->addWeeks(2),
            'country' => 'Slovensko',
            'city' => 'Bratislava',
            'place_name' => 'Sad Janko Krala',
            'place_address' => 'Sad Janka Krala, 851 01 Bratislava',
            'latitude' => 48.1355,
            'longitude' => 17.1070,
            'is_published' => true,
            'published_at' => now()->subDays(3),
            'content' => [
                'sk' => self::masonBricks([
                    ['type' => 'heading', 'level' => 'h2', 'text' => ['sk' => 'O podujatí']],
                    ['type' => 'rich-text', 'content' => ['sk' => '<p>Bezplatný otvorený tréning v Sade Janka Kráľa pre kohokoľvek, kto sa chce hýbať. Príď a vyskúšaj si parkour, kalisteniku alebo jednoducho trénuj s nami vonku. Vítaní sú všetci — od úplných začiatočníkov po skúsených atlétov.</p><p>Tréneri BCZ Club budú k dispozícii po celý čas a pomôžu ti s technikou. Stačí prísť v športovom oblečení a s fľašou vody.</p>']],
                    ['type' => 'heading', 'level' => 'h3', 'text' => ['sk' => 'Čo ťa čaká']],
                    ['type' => 'rich-text', 'content' => ['sk' => '<ul><li>Spoločné zahrievanie a mobility (15 min)</li><li>Rozdelenie do skupín podľa úrovne</li><li>Tréningové stanice — parkour, kalistenika, flexibility (60 min)</li><li>Voľný jam a spoločný tréning (30 min)</li><li>Cool down a stretching</li></ul>']],
                    ['type' => 'quote', 'quote' => ['sk' => '<p>Outdoor tréningy BCZ sú skvelá príležitosť vyskúšať si niečo nové v bezpečnom prostredí. Atmosféra je priateľská a tréneri sú vždy ochotní pomôcť.</p>'], 'attribution' => ['sk' => '— Lucia, pravidelná účastníčka verejných tréningov']],
                ]),
                'en' => self::masonBricks([
                    ['type' => 'heading', 'level' => 'h2', 'text' => ['en' => 'About the Event']],
                    ['type' => 'rich-text', 'content' => ['en' => '<p>Free open training in Sad Janka Kráľa park for anyone who wants to move. Come try parkour, calisthenics, or simply train with us outside. Everyone is welcome — from complete beginners to experienced athletes.</p>']],
                    ['type' => 'heading', 'level' => 'h3', 'text' => ['en' => 'What to Expect']],
                    ['type' => 'rich-text', 'content' => ['en' => '<ul><li>Group warm-up and mobility (15 min)</li><li>Division into groups by level</li><li>Training stations — parkour, calisthenics, flexibility (60 min)</li><li>Free jam and group training (30 min)</li><li>Cool down and stretching</li></ul>']],
                    ['type' => 'quote', 'quote' => ['en' => '<p>BCZ outdoor trainings are a great opportunity to try something new in a safe environment. The atmosphere is friendly and coaches are always willing to help.</p>'], 'attribution' => ['en' => '— Lucia, regular participant of public trainings']],
                ]),
            ],
        ]);
        $publicTraining->addMediaFromUrl('https://picsum.photos/seed/event-park-card/800/450')
            ->toMediaCollection('card_image');
        $publicTraining->addMediaFromUrl('https://picsum.photos/seed/event-park-hero/1440/500')
            ->toMediaCollection('detail_image');
        EventOrganization::factory()->create([
            'event_id' => $publicTraining->id,
            'max_capacity' => 50,
            'pricing_type' => 'free',
            'registration_opens_at' => now()->addDays(3),
            'registration_closes_at' => now()->addWeeks(2)->subDay(),
            'is_public_registration' => true,
            'show_countdown' => true,
        ]);

        // 9. Past organized — paid workshop with external link (finished)
        $pastPaidWorkshop = Event::factory()->organized()->create([
            'event_category_id' => $eventCategories[2]->id,
            'team_id' => $bczTeam->id,
            'title' => ['sk' => 'Tricking Masterclass s Loic Landre', 'en' => 'Tricking Masterclass with Loic Landre', 'cs' => 'Tricking Masterclass s Loic Landre'],
            'card_description' => ['sk' => 'Exkluzivny workshop s medzinarodnym tricking atletom Loic Landre. 4 hodiny intenzivneho treningu.', 'en' => 'Exclusive workshop with international tricking athlete Loic Landre. 4 hours of intensive training.'],
            'date' => now()->subMonths(2),
            'country' => 'Slovensko',
            'city' => 'Bratislava',
            'place_name' => 'BCZ Gym Bratislava',
            'place_address' => 'Stara Vajnorska 37, 831 04 Bratislava',
            'is_published' => true,
            'published_at' => now()->subMonths(4),
            'content' => [
                'sk' => self::masonBricks([
                    ['type' => 'heading', 'level' => 'h2', 'text' => ['sk' => 'O podujatí']],
                    ['type' => 'rich-text', 'content' => ['sk' => '<p>Exkluzívny 4-hodinový tricking masterclass s medzinárodne uznávaným atlétom Loic Landre. Workshop bol zameraný na pokročilé tricking techniky — od základných kickov a twistov po komplexné kombinácie.</p><p>Loic osobne koučoval každého účastníka a zdieľal svoje skúsenosti z medzinárodných súťaží. Kapacita bola limitovaná na 20 miest pre maximálnu kvalitu.</p>']],
                    ['type' => 'heading', 'level' => 'h3', 'text' => ['sk' => 'Program']],
                    ['type' => 'rich-text', 'content' => ['sk' => '<ul><li>Zahrievanie a mobility špeciálne pre tricking (30 min)</li><li>Technika kickov — tornado kick, hook kick, butterfly kick (60 min)</li><li>Twisty a flipy — cork, b-twist, gainer (90 min)</li><li>Kombinácie a vlastný štýl (45 min)</li><li>Q&A a individuálny coaching (15 min)</li></ul>']],
                    ['type' => 'quote', 'quote' => ['sk' => '<p>Workshop s Loicom bol neuveriteľný. Za 4 hodiny som sa naučil viac ako za posledné 3 mesiace sám. Jeho spôsob vysvetľovania je jedinečný.</p>'], 'attribution' => ['sk' => '— Matej K., účastník masterclass']],
                ]),
                'en' => self::masonBricks([
                    ['type' => 'heading', 'level' => 'h2', 'text' => ['en' => 'About the Event']],
                    ['type' => 'rich-text', 'content' => ['en' => '<p>Exclusive 4-hour tricking masterclass with internationally recognized athlete Loic Landre. The workshop focused on advanced tricking techniques — from basic kicks and twists to complex combinations. Limited to 20 spots for maximum quality.</p>']],
                    ['type' => 'heading', 'level' => 'h3', 'text' => ['en' => 'Program']],
                    ['type' => 'rich-text', 'content' => ['en' => '<ul><li>Warm-up and mobility specific to tricking (30 min)</li><li>Kick technique — tornado, hook, butterfly kick (60 min)</li><li>Twists and flips — cork, b-twist, gainer (90 min)</li><li>Combinations and personal style (45 min)</li><li>Q&A and individual coaching (15 min)</li></ul>']],
                    ['type' => 'quote', 'quote' => ['en' => '<p>The workshop with Loic was incredible. I learned more in 4 hours than in the last 3 months on my own.</p>'], 'attribution' => ['en' => '— Matej K., masterclass participant']],
                ]),
            ],
        ]);
        $pastPaidWorkshop->addMediaFromUrl('https://picsum.photos/seed/event-tricking-card/800/450')
            ->toMediaCollection('card_image');
        $pastPaidWorkshop->addMediaFromUrl('https://picsum.photos/seed/event-tricking-hero/1440/500')
            ->toMediaCollection('detail_image');
        EventOrganization::factory()->paid(45.00)->create([
            'event_id' => $pastPaidWorkshop->id,
            'max_capacity' => 20,
            'registration_opens_at' => now()->subMonths(4),
            'registration_closes_at' => now()->subMonths(2)->subWeek(),
            'is_public_registration' => true,
            'external_link' => 'https://forms.google.com/example-tricking-masterclass',
        ]);
        for ($i = 0; $i < 20; $i++) {
            EventRegistration::factory()->create([
                'event_id' => $pastPaidWorkshop->id,
                'user_id' => User::factory()->create()->id,
                'status' => 'approved',
                'registered_at' => now()->subMonths(3)->addDays($i),
            ]);
        }

        // 10. Draft organized — not published yet
        $draftEvent = Event::factory()->organized()->draft()->create([
            'event_category_id' => $eventCategories[2]->id,
            'team_id' => $bczTeam->id,
            'title' => ['sk' => 'BCZ Open Day 2026 (Pripravujeme)', 'en' => 'BCZ Open Day 2026 (Coming Soon)', 'cs' => 'BCZ Open Day 2026 (Pripravujeme)'],
            'card_description' => ['sk' => 'Den otvorenych dveri v BCZ Gym. Ukazy, treningy, sutaze.', 'en' => 'Open house at BCZ Gym. Demos, trainings, competitions.'],
            'date' => now()->addMonths(4),
            'country' => 'Slovensko',
            'city' => 'Bratislava',
            'content' => [
                'sk' => self::masonBricks([
                    ['type' => 'heading', 'level' => 'h2', 'text' => ['sk' => 'O podujatí']],
                    ['type' => 'rich-text', 'content' => ['sk' => '<p>Deň otvorených dverí v BCZ Gym je príležitosť nahliadnuť do sveta parkouru, kalisteniky a trickingu. Pripravili sme celodenný program plný ukážok, otvorených tréningov a malých súťaží pre návštevníkov.</p><p>Či už si nikdy nepočul o parkour alebo trénuješ roky — BCZ Open Day je pre teba. Príď s rodinou, kamarátmi alebo sám a zažijem atmosféru nášho gymu.</p>']],
                    ['type' => 'heading', 'level' => 'h3', 'text' => ['sk' => 'Čo ťa čaká']],
                    ['type' => 'rich-text', 'content' => ['sk' => '<ul><li>10:00 — Otvorenie dverí, prehliadka gymu</li><li>11:00 — Ukážkové vystúpenie atlétov BCZ</li><li>12:00 — Otvorený tréning pre verejnosť (začiatočníci)</li><li>14:00 — Otvorený tréning pre verejnosť (pokročilí)</li><li>15:00 — Mini súťaž pre návštevníkov</li><li>16:00 — Záverečný jam a spoločné foto</li></ul>']],
                ]),
                'en' => self::masonBricks([
                    ['type' => 'heading', 'level' => 'h2', 'text' => ['en' => 'About the Event']],
                    ['type' => 'rich-text', 'content' => ['en' => '<p>Open Day at BCZ Gym is your chance to discover the world of parkour, calisthenics, and tricking. We\'ve prepared a full-day program with demonstrations, open trainings, and mini competitions for visitors.</p>']],
                    ['type' => 'heading', 'level' => 'h3', 'text' => ['en' => 'What to Expect']],
                    ['type' => 'rich-text', 'content' => ['en' => '<ul><li>10:00 — Doors open, gym tour</li><li>11:00 — BCZ athletes demo performance</li><li>12:00 — Open training for public (beginners)</li><li>14:00 — Open training for public (advanced)</li><li>15:00 — Mini competition for visitors</li><li>16:00 — Final jam and group photo</li></ul>']],
                ]),
            ],
        ]);
        $draftEvent->addMediaFromUrl('https://picsum.photos/seed/event-openday-card/800/450')
            ->toMediaCollection('card_image');
        $draftEvent->addMediaFromUrl('https://picsum.photos/seed/event-openday-hero/1440/500')
            ->toMediaCollection('detail_image');
        EventOrganization::factory()->create([
            'event_id' => $draftEvent->id,
            'max_capacity' => 100,
            'pricing_type' => 'free',
        ]);

        // --- Disciplines ---
        $disciplines = collect([
            [
                'name' => ['sk' => 'Statika', 'en' => 'Statics'],
                'description' => ['sk' => 'Statické prvky a výdrže.', 'en' => 'Static elements and holds.'],
                'sort_order' => 1,
            ],
            [
                'name' => ['sk' => 'Dynamika', 'en' => 'Dynamics'],
                'description' => ['sk' => 'Dynamické pohyby a akrobatické prvky.', 'en' => 'Dynamic movements and acrobatic elements.'],
                'sort_order' => 2,
            ],
            [
                'name' => ['sk' => 'Kombinácie', 'en' => 'Combos'],
                'description' => ['sk' => 'Kombinácie rôznych prvkov do plynulých zostáv.', 'en' => 'Combinations of various elements into fluid routines.'],
                'sort_order' => 3,
            ],
            [
                'name' => ['sk' => 'Silová dynamika', 'en' => 'Strength dynamics'],
                'description' => ['sk' => 'Silové dynamické prvky.', 'en' => 'Strength-based dynamic elements.'],
                'sort_order' => 4,
            ],
            [
                'name' => ['sk' => 'Statické výdrže', 'en' => 'Static holds'],
                'description' => ['sk' => 'Výdrže v statických pozíciách.', 'en' => 'Holds in static positions.'],
                'sort_order' => 5,
            ],
            [
                'name' => ['sk' => 'Kalistenika', 'en' => 'Calisthenics'],
                'description' => ['sk' => 'Cvičenie s vlastnou váhou tela.', 'en' => 'Bodyweight exercise training.'],
                'sort_order' => 6,
            ],
            [
                'name' => ['sk' => 'Parkour', 'en' => 'Parkour'],
                'description' => ['sk' => 'Prekonávanie prekážok efektívnym pohybom.', 'en' => 'Overcoming obstacles through efficient movement.'],
                'sort_order' => 7,
            ],
        ])->map(fn ($data) => Discipline::factory()->create($data));

        // --- Athlete Categories ---
        $menCategory = AthleteCategory::factory()->create([
            'name' => ['sk' => 'Muži', 'en' => 'Men'],
            'gender' => GenderEnum::MALE,
            'sort_order' => 1,
        ]);
        $womenCategory = AthleteCategory::factory()->create([
            'name' => ['sk' => 'Ženy', 'en' => 'Women'],
            'gender' => GenderEnum::FEMALE,
            'sort_order' => 2,
        ]);
        $youthCategory = AthleteCategory::factory()->create([
            'name' => ['sk' => 'Juniori', 'en' => 'Juniors'],
            'min_age' => 14,
            'max_age' => 17,
            'sort_order' => 3,
        ]);

        $allCategories = collect([$menCategory, $womenCategory, $youthCategory]);

        // =============================================
        // --- COMPETITIONS (unified event system) ---
        // =============================================

        // ===== COMPETITION 1: Past — BCZ Championship 2025 (POINTS-based, fully finished) =====
        $pastCompetition = Event::factory()->competition()->create([
            'title' => ['sk' => 'BCZ Championship 2025', 'en' => 'BCZ Championship 2025', 'cs' => 'BCZ Championship 2025'],
            'card_description' => ['sk' => 'Hlavna sutaz sezony 2025. Statika, dynamika a kombinacie v troch vekovych kategoriach.', 'en' => 'Main competition of the 2025 season. Statics, dynamics and combos in three age categories.'],
            'team_id' => $bczTeam->id,
            'event_category_id' => $eventCategories[0]->id,
            'date' => now()->subMonths(3),
            'date_end' => now()->subMonths(3)->addDay(),
            'place_name' => 'Sportova hala Pasienky',
            'place_address' => 'Junacka 6, 831 04 Bratislava',
            'country' => 'Slovensko',
            'city' => 'Bratislava',
            'latitude' => 48.1660,
            'longitude' => 17.1350,
            'is_published' => true,
            'published_at' => now()->subMonths(5),
            'content' => [
                'sk' => self::masonBricks([
                    ['type' => 'heading', 'level' => 'h2', 'text' => ['sk' => 'O súťaži']],
                    ['type' => 'rich-text', 'content' => ['sk' => '<p>BCZ Championship 2025 je hlavná súťaž sezóny organizovaná BCZ Club. Súťažiaci sa predvedú v disciplínach statika, dynamika a kombinácie pred panelom 5 rozhodcov. Súťaží sa v troch vekových kategóriách — juniori, muži a ženy.</p><p>Bodovací systém je transparentný — každý rozhodca hodnotí techniku, kreativitu a obtiažnosť. Výsledky sa zverejňujú priebežne počas celej súťaže.</p>']],
                    ['type' => 'heading', 'level' => 'h3', 'text' => ['sk' => 'Formát súťaže']],
                    ['type' => 'rich-text', 'content' => ['sk' => '<p>Súťaž prebieha v dvoch kolách. V kvalifikácii má každý súťažiaci 90 sekúnd na predvedenie zostavy. Top 8 z každej kategórie postupujú do finále, kde majú 120 sekúnd. Hodnotí sa technika (40%), kreativita (30%) a obtiažnosť (30%).</p>']],
                    ['type' => 'stats', 'badge' => ['sk' => 'Štatistiky'], 'badge_color' => 'primary', 'title' => ['sk' => 'Championship v číslach'], 'items' => [
                        ['number' => '80', 'label' => ['sk' => 'Súťažiacich'], 'color' => 'primary', 'icon' => 'heroicon-o-users'],
                        ['number' => '3', 'label' => ['sk' => 'Kategórie'], 'color' => 'success', 'icon' => 'heroicon-o-trophy'],
                        ['number' => '5', 'label' => ['sk' => 'Disciplín'], 'color' => 'warning', 'icon' => 'heroicon-o-fire'],
                        ['number' => '5', 'label' => ['sk' => 'Rozhodcov'], 'color' => 'danger', 'icon' => 'heroicon-o-clipboard-document-check'],
                    ]],
                ]),
                'en' => self::masonBricks([
                    ['type' => 'heading', 'level' => 'h2', 'text' => ['en' => 'About the Competition']],
                    ['type' => 'rich-text', 'content' => ['en' => '<p>BCZ Championship 2025 is the main competition of the season organized by BCZ Club. Competitors perform in statics, dynamics, and combos disciplines in front of a panel of 5 judges across three age categories.</p>']],
                    ['type' => 'heading', 'level' => 'h3', 'text' => ['en' => 'Competition Format']],
                    ['type' => 'rich-text', 'content' => ['en' => '<p>The competition has two rounds. In qualification, each competitor has 90 seconds to perform a routine. Top 8 from each category advance to finals with 120 seconds. Scoring: technique (40%), creativity (30%), difficulty (30%).</p>']],
                    ['type' => 'stats', 'badge' => ['en' => 'Statistics'], 'badge_color' => 'primary', 'title' => ['en' => 'Championship in Numbers'], 'items' => [
                        ['number' => '80', 'label' => ['en' => 'Competitors'], 'color' => 'primary', 'icon' => 'heroicon-o-users'],
                        ['number' => '3', 'label' => ['en' => 'Categories'], 'color' => 'success', 'icon' => 'heroicon-o-trophy'],
                        ['number' => '5', 'label' => ['en' => 'Disciplines'], 'color' => 'warning', 'icon' => 'heroicon-o-fire'],
                        ['number' => '5', 'label' => ['en' => 'Judges'], 'color' => 'danger', 'icon' => 'heroicon-o-clipboard-document-check'],
                    ]],
                ]),
            ],
            'report_content' => [
                'sk' => self::masonBricks([
                    ['type' => 'heading', 'level' => 'h2', 'text' => ['sk' => 'Report z BCZ Championship 2025']],
                    ['type' => 'rich-text', 'content' => ['sk' => '<p>BCZ Championship 2025 sa uskutočnil 1. januára 2026 v Športovej hale Pasienky v Bratislave. Zúčastnilo sa 60 atletov z 3 krajín v kategóriách juniori, muži a ženy. Súťaž prebiehala v disciplínach statika, dynamika a kombinácie.</p><p>Atmosféra bola elektrizujúca — plná hala fanúšikov podporovala súťažiacich od kvalifikácie až po finálové zostavy. Rozhodcovský panel sa skladal z 5 medzinárodne certifikovaných porotcov.</p>']],
                    ['type' => 'heading', 'level' => 'h3', 'text' => ['sk' => 'Najlepšie momenty']],
                    ['type' => 'rich-text', 'content' => ['sk' => '<ul><li>Dominik Klimek obhájil titul v kategórii mužov s rekordným skóre 94.5 bodu</li><li>V juniorskej kategórii sa predstavili aj úplní nováčikovia — najnižší vek 14 rokov</li><li>Ženská kategória zaznamenala najväčší nárast účasti — o 40% viac súťažiacich oproti minulému roku</li><li>Prvýkrát sa predstavila disciplína kombinácie s live hudbou</li></ul>']],
                    ['type' => 'quote', 'quote' => ['sk' => '<p>Championship 2025 ukázal, že slovenská kalistenická scéna rastie neuveriteľným tempom. Úroveň výkonov sa rok od roku zvyšuje a my sme hrdí, že môžeme byť súčasťou tejto cesty.</p>'], 'attribution' => ['sk' => '— Dominik Klimek, zakladateľ BCZ Club']],
                ]),
                'en' => self::masonBricks([
                    ['type' => 'heading', 'level' => 'h2', 'text' => ['en' => 'BCZ Championship 2025 Report']],
                    ['type' => 'rich-text', 'content' => ['en' => '<p>BCZ Championship 2025 took place on January 1, 2026 at Sportova hala Pasienky in Bratislava. 60 athletes from 3 countries competed in juniors, men, and women categories across statics, dynamics, and combos disciplines.</p>']],
                    ['type' => 'heading', 'level' => 'h3', 'text' => ['en' => 'Highlights']],
                    ['type' => 'rich-text', 'content' => ['en' => '<ul><li>Dominik Klimek defended his title in the men\'s category with a record score of 94.5 points</li><li>The junior category featured complete beginners — youngest age 14</li><li>Women\'s category saw the biggest growth — 40% more competitors than last year</li></ul>']],
                ]),
            ],
        ]);
        $pastCompetition->addMediaFromUrl('https://picsum.photos/seed/event-championship-card/800/450')
            ->toMediaCollection('card_image');
        $pastCompetition->addMediaFromUrl('https://picsum.photos/seed/event-championship-hero/1440/500')
            ->toMediaCollection('detail_image');
        EventOrganization::factory()->paid(25.00)->create([
            'event_id' => $pastCompetition->id,
            'max_capacity' => 80,
            'registration_opens_at' => now()->subMonths(5),
            'registration_closes_at' => now()->subMonths(3)->subWeek(),
            'is_public_registration' => true,
        ]);
        $pastCompDetail = CompetitionDetail::factory()->create(['event_id' => $pastCompetition->id]);

        // ===== COMPETITION 2: Past — Street Workout Battle Kosice (BATTLE-based, brackets) =====
        $battleCompetition = Event::factory()->competition()->create([
            'title' => ['sk' => 'Street Workout Battle Kosice', 'en' => 'Street Workout Battle Kosice', 'cs' => 'Street Workout Battle Kosice'],
            'card_description' => ['sk' => '1v1 battle format. 16 atletov, eliminacne kola az po finale. Cisty street workout.', 'en' => '1v1 battle format. 16 athletes, elimination rounds to the finals. Pure street workout.'],
            'team_id' => $bczTeam->id,
            'event_category_id' => $eventCategories[0]->id,
            'date' => now()->subMonths(1),
            'country' => 'Slovensko',
            'city' => 'Kosice',
            'place_name' => 'Workout Park Kosice',
            'place_address' => 'Hlavna 1, 040 01 Kosice',
            'latitude' => 48.7164,
            'longitude' => 21.2611,
            'is_published' => true,
            'published_at' => now()->subMonths(3),
            'content' => [
                'sk' => self::masonBricks([
                    ['type' => 'heading', 'level' => 'h2', 'text' => ['sk' => 'O súťaži']],
                    ['type' => 'rich-text', 'content' => ['sk' => '<p>Street Workout Battle Košice je adrenalínová 1v1 battle súťaž v čistom street workout štýle. 16 atlétov sa stretne v eliminačných kolách — od osemfinále až po veľké finále na outdoor workout parku v centre Košíc.</p><p>Každý battle trvá 2x45 sekúnd. Rozhodcovia hodnotia techniku, silu, kreativitu a showmanship. Atmosféra je elektrická — publikum je súčasťou zážitku.</p>']],
                    ['type' => 'heading', 'level' => 'h3', 'text' => ['sk' => 'Pravidlá']],
                    ['type' => 'rich-text', 'content' => ['sk' => '<p>Battle formát: 1v1 eliminácia. Každý atléta má dve 45-sekundové kolá. Rozhodcovia (3) hodnotia zdvihnutím karty. Víťaz postupuje do ďalšieho kola. Zakázané sú nebezpečné prvky mimo matrace a kontakt s protivníkom.</p>']],
                    ['type' => 'stats', 'badge' => ['sk' => 'Štatistiky'], 'badge_color' => 'warning', 'title' => ['sk' => 'Battle v číslach'], 'items' => [
                        ['number' => '16', 'label' => ['sk' => 'Súťažiacich'], 'color' => 'primary', 'icon' => 'heroicon-o-users'],
                        ['number' => '4', 'label' => ['sk' => 'Kolá'], 'color' => 'success', 'icon' => 'heroicon-o-arrow-path'],
                        ['number' => '3', 'label' => ['sk' => 'Rozhodcov'], 'color' => 'warning', 'icon' => 'heroicon-o-clipboard-document-check'],
                        ['number' => '1', 'label' => ['sk' => 'Šampión'], 'color' => 'danger', 'icon' => 'heroicon-o-trophy'],
                    ]],
                ]),
                'en' => self::masonBricks([
                    ['type' => 'heading', 'level' => 'h2', 'text' => ['en' => 'About the Competition']],
                    ['type' => 'rich-text', 'content' => ['en' => '<p>Street Workout Battle Košice is an adrenaline-fueled 1v1 battle competition in pure street workout style. 16 athletes meet in elimination rounds from round of 16 to the grand final at an outdoor workout park in Košice city center.</p>']],
                    ['type' => 'heading', 'level' => 'h3', 'text' => ['en' => 'Rules']],
                    ['type' => 'rich-text', 'content' => ['en' => '<p>Battle format: 1v1 elimination. Each athlete has two 45-second rounds. Judges (3) score by raising cards. Winner advances. Dangerous elements off mats and contact with opponent are prohibited.</p>']],
                    ['type' => 'stats', 'badge' => ['en' => 'Statistics'], 'badge_color' => 'warning', 'title' => ['en' => 'Battle in Numbers'], 'items' => [
                        ['number' => '16', 'label' => ['en' => 'Competitors'], 'color' => 'primary', 'icon' => 'heroicon-o-users'],
                        ['number' => '4', 'label' => ['en' => 'Rounds'], 'color' => 'success', 'icon' => 'heroicon-o-arrow-path'],
                        ['number' => '3', 'label' => ['en' => 'Judges'], 'color' => 'warning', 'icon' => 'heroicon-o-clipboard-document-check'],
                        ['number' => '1', 'label' => ['en' => 'Champion'], 'color' => 'danger', 'icon' => 'heroicon-o-trophy'],
                    ]],
                ]),
            ],
        ]);
        $battleCompetition->addMediaFromUrl('https://picsum.photos/seed/event-battle-card/800/450')
            ->toMediaCollection('card_image');
        $battleCompetition->addMediaFromUrl('https://picsum.photos/seed/event-battle-hero/1440/500')
            ->toMediaCollection('detail_image');
        EventOrganization::factory()->paid(15.00)->create([
            'event_id' => $battleCompetition->id,
            'max_capacity' => 32,
            'registration_opens_at' => now()->subMonths(3),
            'registration_closes_at' => now()->subMonths(1)->subWeek(),
            'is_public_registration' => true,
        ]);
        $battleCompDetail = CompetitionDetail::factory()->create(['event_id' => $battleCompetition->id]);

        // ===== COMPETITION 3: Upcoming — BCZ Spring Cup 2026 (registration open) =====
        $upcomingCompetition = Event::factory()->competition()->create([
            'title' => ['sk' => 'BCZ Spring Cup 2026', 'en' => 'BCZ Spring Cup 2026', 'cs' => 'BCZ Spring Cup 2026'],
            'card_description' => ['sk' => 'Jarna sutaz pre vsetky vekove kategorie. Bodovaci system + battle finale.', 'en' => 'Spring competition for all age categories. Points system + battle finals.'],
            'team_id' => $bczTeam->id,
            'event_category_id' => $eventCategories[0]->id,
            'date' => now()->addMonths(2),
            'date_end' => now()->addMonths(2)->addDay(),
            'place_name' => 'Outdoor Park Kosice',
            'place_address' => 'Hlavna 1, 040 01 Kosice',
            'country' => 'Slovensko',
            'city' => 'Kosice',
            'latitude' => 48.7164,
            'longitude' => 21.2611,
            'is_published' => true,
            'published_at' => now()->subWeek(),
            'content' => [
                'sk' => self::masonBricks([
                    ['type' => 'heading', 'level' => 'h2', 'text' => ['sk' => 'O súťaži']],
                    ['type' => 'rich-text', 'content' => ['sk' => '<p>BCZ Spring Cup 2026 je jarná súťaž pre všetky vekové kategórie. Kombinuje bodovací systém v kvalifikácii s battle finále pre top 4 atlétov z každej kategórie. Súťaží sa outdoorovo v Košiciach.</p><p>Registrácia je otvorená pre všetkých atlétov bez ohľadu na klubovú príslušnosť. Cena zahŕňa štartovné, tričko a obed.</p>']],
                    ['type' => 'heading', 'level' => 'h3', 'text' => ['sk' => 'Formát súťaže']],
                    ['type' => 'rich-text', 'content' => ['sk' => '<p>Kvalifikácia: bodovací systém, každý súťažiaci má 90 sekúnd. Top 4 z každej kategórie postupujú do battle finále. Battle finále: semifinále a finále v 1v1 formáte. Celkový víťaz získava titul BCZ Spring Cup Champion.</p>']],
                    ['type' => 'stats', 'badge' => ['sk' => 'Štatistiky'], 'badge_color' => 'success', 'title' => ['sk' => 'Spring Cup v číslach'], 'items' => [
                        ['number' => '60', 'label' => ['sk' => 'Max. kapacita'], 'color' => 'primary', 'icon' => 'heroicon-o-users'],
                        ['number' => '3', 'label' => ['sk' => 'Kategórie'], 'color' => 'success', 'icon' => 'heroicon-o-trophy'],
                        ['number' => '2', 'label' => ['sk' => 'Dni'], 'color' => 'warning', 'icon' => 'heroicon-o-calendar'],
                        ['number' => '25 EUR', 'label' => ['sk' => 'Štartovné'], 'color' => 'danger', 'icon' => 'heroicon-o-currency-euro'],
                    ]],
                ]),
                'en' => self::masonBricks([
                    ['type' => 'heading', 'level' => 'h2', 'text' => ['en' => 'About the Competition']],
                    ['type' => 'rich-text', 'content' => ['en' => '<p>BCZ Spring Cup 2026 is a spring competition for all age categories combining a points-based qualification with battle finals for top 4 athletes from each category. Outdoor venue in Košice.</p>']],
                    ['type' => 'heading', 'level' => 'h3', 'text' => ['en' => 'Competition Format']],
                    ['type' => 'rich-text', 'content' => ['en' => '<p>Qualification: points system, 90 seconds per competitor. Top 4 advance to battle finals. Battle finals: semi-finals and finals in 1v1 format. Overall winner earns the BCZ Spring Cup Champion title.</p>']],
                    ['type' => 'stats', 'badge' => ['en' => 'Statistics'], 'badge_color' => 'success', 'title' => ['en' => 'Spring Cup in Numbers'], 'items' => [
                        ['number' => '60', 'label' => ['en' => 'Max. capacity'], 'color' => 'primary', 'icon' => 'heroicon-o-users'],
                        ['number' => '3', 'label' => ['en' => 'Categories'], 'color' => 'success', 'icon' => 'heroicon-o-trophy'],
                        ['number' => '2', 'label' => ['en' => 'Days'], 'color' => 'warning', 'icon' => 'heroicon-o-calendar'],
                        ['number' => '25 EUR', 'label' => ['en' => 'Entry fee'], 'color' => 'danger', 'icon' => 'heroicon-o-currency-euro'],
                    ]],
                ]),
            ],
        ]);
        $upcomingCompetition->addMediaFromUrl('https://picsum.photos/seed/event-springcup-card/800/450')
            ->toMediaCollection('card_image');
        $upcomingCompetition->addMediaFromUrl('https://picsum.photos/seed/event-springcup-hero/1440/500')
            ->toMediaCollection('detail_image');
        for ($g = 1; $g <= 8; $g++) {
            $upcomingCompetition->addMediaFromUrl("https://picsum.photos/seed/springcup-gallery-{$g}/800/600")
                ->toMediaCollection('gallery');
        }
        EventOrganization::factory()->paid(25.00)->create([
            'event_id' => $upcomingCompetition->id,
            'max_capacity' => 60,
            'registration_opens_at' => now()->subWeek(),
            'registration_closes_at' => now()->addMonth(),
            'is_public_registration' => true,
            'show_countdown' => true,
            'registration_form_schema' => [
                ['key' => 'first_name', 'type' => 'first_name', 'label' => 'Meno', 'required' => true],
                ['key' => 'last_name', 'type' => 'last_name', 'label' => 'Priezvisko', 'required' => true],
                ['key' => 'email', 'type' => 'email', 'label' => 'E-mail', 'required' => true],
                ['key' => 'phone', 'type' => 'phone', 'label' => 'Telefón', 'required' => true],
                ['key' => 'birth_date', 'type' => 'birth_date', 'label' => 'Dátum narodenia', 'required' => true],
                ['key' => 'gender', 'type' => 'gender', 'label' => 'Pohlavie', 'required' => true],
                ['key' => 'club', 'type' => 'text_input', 'label' => 'Klub / Tím', 'required' => false],
                ['key' => 'weight', 'type' => 'number_input', 'label' => 'Váha (kg)', 'required' => false],
                ['key' => 'experience', 'type' => 'select', 'label' => 'Úroveň skúseností', 'required' => true, 'options' => "Začiatočník\nMierne pokročilý\nPokročilý\nProfesionál"],
                ['key' => 'notes', 'type' => 'textarea', 'label' => 'Poznámka', 'required' => false],
            ],
        ]);
        $upcomingCompDetail = CompetitionDetail::factory()->create(['event_id' => $upcomingCompetition->id]);

        // Rounds for upcoming BCZ Spring Cup
        $springQualRound = CompetitionRound::factory()->create([
            'competition_detail_id' => $upcomingCompDetail->id,
            'athlete_category_id' => $menCategory->id,
            'round_number' => 1,
            'name' => 'Kvalifikácia',
            'scoring_format' => 'points',
            'advancement_type' => RoundAdvancementTypeEnum::TOP_BY_POINTS,
            'sort_order' => 1,
        ]);
        foreach (['Technika', 'Kreativita', 'Obtiažnosť'] as $i => $name) {
            RoundPart::factory()->create([
                'competition_round_id' => $springQualRound->id,
                'name' => ['sk' => $name, 'en' => $name, 'cs' => $name],
                'duration_seconds' => 90,
                'sort_order' => $i + 1,
            ]);
        }
        CompetitionRound::factory()->create([
            'competition_detail_id' => $upcomingCompDetail->id,
            'athlete_category_id' => $menCategory->id,
            'round_number' => 2,
            'name' => 'Battle finále',
            'scoring_format' => 'points',
            'advancement_type' => RoundAdvancementTypeEnum::BATTLE_WINNER,
            'sort_order' => 2,
        ]);

        // ===== COMPETITION 4: Past — Free community comp (free entry, points only, no battles) =====
        $freeCompetition = Event::factory()->competition()->create([
            'title' => ['sk' => 'BCZ Community Jam 2025', 'en' => 'BCZ Community Jam 2025', 'cs' => 'BCZ Community Jam 2025'],
            'card_description' => ['sk' => 'Neformalna sutaz pre komunitu. Zadarmo, len pre zabavu a rozvoj. Vsetky urovne vitane.', 'en' => 'Informal community competition. Free, just for fun and development. All levels welcome.'],
            'team_id' => $bczTeam->id,
            'event_category_id' => $eventCategories[2]->id,
            'date' => now()->subMonths(5),
            'country' => 'Slovensko',
            'city' => 'Cadca',
            'place_name' => 'Mestsky park Cadca',
            'place_address' => 'Mestsky park, 022 01 Cadca',
            'latitude' => 49.4405,
            'longitude' => 18.7863,
            'is_published' => true,
            'published_at' => now()->subMonths(7),
            'content' => [
                'sk' => self::masonBricks([
                    ['type' => 'heading', 'level' => 'h2', 'text' => ['sk' => 'O súťaži']],
                    ['type' => 'rich-text', 'content' => ['sk' => '<p>BCZ Community Jam je neformálna komunitná súťaž určená pre všetkých — od začiatočníkov po pokročilých. Žiadne štartovné, žiadny stres, len čistá radosť z pohybu a priateľská súťaživosť.</p><p>Cieľom Community Jamu nie je nájsť najlepšieho atléta, ale motivovať každého účastníka k osobnému posunu. Každý, kto súťaží, dostane spätnú väzbu od rozhodcov.</p>']],
                    ['type' => 'heading', 'level' => 'h3', 'text' => ['sk' => 'Formát súťaže']],
                    ['type' => 'rich-text', 'content' => ['sk' => '<p>Jednoduchý bodovací systém. Každý súťažiaci má 60 sekúnd na svoju zostavu. Rozhodcovia hodnotia snahu, kreativitu a pokrok. Žiadne eliminácie — každý súťaží a každý dostane hodnotenie. Ocenenie pre Top 3 a špeciálne ceny za najlepší pokrok.</p>']],
                    ['type' => 'stats', 'badge' => ['sk' => 'Štatistiky'], 'badge_color' => 'info', 'title' => ['sk' => 'Community Jam v číslach'], 'items' => [
                        ['number' => '20', 'label' => ['sk' => 'Súťažiacich'], 'color' => 'primary', 'icon' => 'heroicon-o-users'],
                        ['number' => '0 EUR', 'label' => ['sk' => 'Štartovné'], 'color' => 'success', 'icon' => 'heroicon-o-currency-euro'],
                        ['number' => '1', 'label' => ['sk' => 'Deň'], 'color' => 'warning', 'icon' => 'heroicon-o-calendar'],
                        ['number' => '100%', 'label' => ['sk' => 'Zábava'], 'color' => 'danger', 'icon' => 'heroicon-o-face-smile'],
                    ]],
                ]),
                'en' => self::masonBricks([
                    ['type' => 'heading', 'level' => 'h2', 'text' => ['en' => 'About the Competition']],
                    ['type' => 'rich-text', 'content' => ['en' => '<p>BCZ Community Jam is an informal community competition for everyone — from beginners to advanced athletes. No entry fee, no stress, just pure joy of movement and friendly competition.</p>']],
                    ['type' => 'heading', 'level' => 'h3', 'text' => ['en' => 'Competition Format']],
                    ['type' => 'rich-text', 'content' => ['en' => '<p>Simple points system. Each competitor has 60 seconds for their routine. Judges score effort, creativity, and progress. No eliminations — everyone competes and receives feedback. Awards for Top 3 and special prizes for best improvement.</p>']],
                    ['type' => 'stats', 'badge' => ['en' => 'Statistics'], 'badge_color' => 'info', 'title' => ['en' => 'Community Jam in Numbers'], 'items' => [
                        ['number' => '20', 'label' => ['en' => 'Competitors'], 'color' => 'primary', 'icon' => 'heroicon-o-users'],
                        ['number' => '0 EUR', 'label' => ['en' => 'Entry fee'], 'color' => 'success', 'icon' => 'heroicon-o-currency-euro'],
                        ['number' => '1', 'label' => ['en' => 'Day'], 'color' => 'warning', 'icon' => 'heroicon-o-calendar'],
                        ['number' => '100%', 'label' => ['en' => 'Fun'], 'color' => 'danger', 'icon' => 'heroicon-o-face-smile'],
                    ]],
                ]),
            ],
        ]);
        $freeCompetition->addMediaFromUrl('https://picsum.photos/seed/event-jam-card/800/450')
            ->toMediaCollection('card_image');
        $freeCompetition->addMediaFromUrl('https://picsum.photos/seed/event-jam-hero/1440/500')
            ->toMediaCollection('detail_image');
        EventOrganization::factory()->create([
            'event_id' => $freeCompetition->id,
            'max_capacity' => 20,
            'pricing_type' => 'free',
            'registration_opens_at' => now()->subMonths(7),
            'registration_closes_at' => now()->subMonths(5)->subWeek(),
            'is_public_registration' => true,
        ]);
        $freeCompDetail = CompetitionDetail::factory()->create(['event_id' => $freeCompetition->id]);

        // ===== COMPETITION 5: Upcoming — International event (registration not yet open) =====
        $futureCompetition = Event::factory()->competition()->create([
            'title' => ['sk' => 'Central European Calisthenics Open', 'en' => 'Central European Calisthenics Open', 'cs' => 'Central European Calisthenics Open'],
            'card_description' => ['sk' => 'Medzinarodna sutaz pre krajiny strednej Europy. SK, CZ, PL, HU, AT. Bodovy system + battle finale.', 'en' => 'International competition for Central European countries. SK, CZ, PL, HU, AT. Points system + battle finals.'],
            'team_id' => $bczTeam->id,
            'event_category_id' => $eventCategories[0]->id,
            'date' => now()->addMonths(5),
            'date_end' => now()->addMonths(5)->addDays(2),
            'place_name' => 'X-Bionic Sphere',
            'place_address' => 'Dubova 33/A, 931 01 Samorin',
            'country' => 'Slovensko',
            'city' => 'Samorin',
            'latitude' => 47.8614,
            'longitude' => 17.3089,
            'is_published' => true,
            'published_at' => now()->subDays(5),
            'content' => [
                'sk' => self::masonBricks([
                    ['type' => 'heading', 'level' => 'h2', 'text' => ['sk' => 'O súťaži']],
                    ['type' => 'rich-text', 'content' => ['sk' => '<p>Central European Calisthenics Open je medzinárodná súťaž pre krajiny strednej Európy — Slovensko, Česko, Poľsko, Maďarsko a Rakúsko. Tri dni intenzívnej súťaže v prestížnom X-Bionic Sphere v Šamoríne.</p><p>Súťaž kombinuje bodovací systém v kvalifikáciách s battle finále. Najlepší atléti z každej krajiny sa stretnú v boji o titul Central European Champion. Súčasťou programu sú aj workshopy a networking.</p>']],
                    ['type' => 'heading', 'level' => 'h3', 'text' => ['sk' => 'Formát súťaže']],
                    ['type' => 'rich-text', 'content' => ['sk' => '<p>Deň 1: Registrácia a otvorené tréningy. Deň 2: Kvalifikačné kolá vo všetkých disciplínach (bodovací systém). Deň 3: Battle finále Top 8 v každej kategórii + vyhlásenie výsledkov a afterparty. Medzinárodný panel 7 rozhodcov.</p>']],
                    ['type' => 'stats', 'badge' => ['sk' => 'Štatistiky'], 'badge_color' => 'danger', 'title' => ['sk' => 'Central European Open v číslach'], 'items' => [
                        ['number' => '120', 'label' => ['sk' => 'Max. kapacita'], 'color' => 'primary', 'icon' => 'heroicon-o-users'],
                        ['number' => '5', 'label' => ['sk' => 'Krajín'], 'color' => 'success', 'icon' => 'heroicon-o-globe-alt'],
                        ['number' => '3', 'label' => ['sk' => 'Dni'], 'color' => 'warning', 'icon' => 'heroicon-o-calendar'],
                        ['number' => '7', 'label' => ['sk' => 'Rozhodcov'], 'color' => 'danger', 'icon' => 'heroicon-o-clipboard-document-check'],
                    ]],
                ]),
                'en' => self::masonBricks([
                    ['type' => 'heading', 'level' => 'h2', 'text' => ['en' => 'About the Competition']],
                    ['type' => 'rich-text', 'content' => ['en' => '<p>Central European Calisthenics Open is an international competition for Central European countries — Slovakia, Czech Republic, Poland, Hungary, and Austria. Three days of intense competition at the prestigious X-Bionic Sphere in Šamorín.</p>']],
                    ['type' => 'heading', 'level' => 'h3', 'text' => ['en' => 'Competition Format']],
                    ['type' => 'rich-text', 'content' => ['en' => '<p>Day 1: Registration and open training. Day 2: Qualification rounds in all disciplines (points system). Day 3: Battle finals Top 8 per category + awards ceremony and afterparty. International panel of 7 judges.</p>']],
                    ['type' => 'stats', 'badge' => ['en' => 'Statistics'], 'badge_color' => 'danger', 'title' => ['en' => 'Central European Open in Numbers'], 'items' => [
                        ['number' => '120', 'label' => ['en' => 'Max. capacity'], 'color' => 'primary', 'icon' => 'heroicon-o-users'],
                        ['number' => '5', 'label' => ['en' => 'Countries'], 'color' => 'success', 'icon' => 'heroicon-o-globe-alt'],
                        ['number' => '3', 'label' => ['en' => 'Days'], 'color' => 'warning', 'icon' => 'heroicon-o-calendar'],
                        ['number' => '7', 'label' => ['en' => 'Judges'], 'color' => 'danger', 'icon' => 'heroicon-o-clipboard-document-check'],
                    ]],
                ]),
            ],
        ]);
        $futureCompetition->addMediaFromUrl('https://picsum.photos/seed/event-ceco-card/800/450')
            ->toMediaCollection('card_image');
        $futureCompetition->addMediaFromUrl('https://picsum.photos/seed/event-ceco-hero/1440/500')
            ->toMediaCollection('detail_image');
        EventOrganization::factory()->paid(40.00)->create([
            'event_id' => $futureCompetition->id,
            'max_capacity' => 120,
            'registration_opens_at' => now()->addMonth(),
            'registration_closes_at' => now()->addMonths(4),
            'is_public_registration' => true,
            'show_countdown' => true,
        ]);
        $futureCompDetail = CompetitionDetail::factory()->create(['event_id' => $futureCompetition->id]);

        // ===== COMPETITION 6: TODAY — BCZ Live Cup 2026 (IN PROGRESS, registration + weigh-in phase) =====
        $todayCompetition = Event::factory()->competition()->create([
            'title' => ['sk' => 'BCZ Live Cup 2026', 'en' => 'BCZ Live Cup 2026', 'cs' => 'BCZ Live Cup 2026'],
            'card_description' => [
                'sk' => 'Celodenná súťaž s kvalifikáciou aj battle finále. Tri kategórie, päť disciplín, medzinárodný panel rozhodcov.',
                'en' => 'Full-day competition with qualification and battle finals. Three categories, five disciplines, international judging panel.',
                'cs' => 'Celodenní soutěž s kvalifikací a battle finále. Tři kategorie, pět disciplín, mezinárodní panel rozhodčích.',
            ],
            'team_id' => $bczTeam->id,
            'event_category_id' => $eventCategories[0]->id,
            'date' => today(),
            'date_end' => today()->addDay(),
            'place_name' => 'NTC Bratislava',
            'place_address' => 'Príkopova 6, 831 03 Bratislava',
            'country' => 'Slovensko',
            'city' => 'Bratislava',
            'latitude' => 48.1530,
            'longitude' => 17.1312,
            'is_published' => true,
            'published_at' => now()->subMonth(),
            'content' => [
                'sk' => self::masonBricks([
                    ['type' => 'heading', 'level' => 'h2', 'text' => ['sk' => 'O súťaži']],
                    ['type' => 'rich-text', 'content' => ['sk' => '<p>BCZ Live Cup 2026 je celodenná kalistenická súťaž kombinujúca bodovací systém v kvalifikáciách s battle finále. Súťaží sa v troch kategóriách — muži, ženy a juniori — v piatich disciplínach pred panelom 5 rozhodcov.</p><p>Kvalifikácia prebieha bodovacím systémom (technika, kreativita, obtiažnosť). Top 4 z každej kategórie postupujú do battle finále v 1v1 formáte.</p>']],
                    ['type' => 'heading', 'level' => 'h3', 'text' => ['sk' => 'Bodovací systém']],
                    ['type' => 'rich-text', 'content' => ['sk' => '<p><strong>Kvalifikácia:</strong> Každý súťažiaci má 90 sekúnd. Hodnotí sa technika (40%), kreativita (30%) a obtiažnosť (30%). Maximum 100 bodov.<br><strong>Battle finále:</strong> 1v1 eliminácia, 2x45 sekúnd. Rozhodcovia hlasujú zdvihnutím karty — väčšina rozhoduje.</p>']],
                    ['type' => 'stats', 'badge' => ['sk' => 'Dnes'], 'badge_color' => 'warning', 'title' => ['sk' => 'Live Cup v číslach'], 'items' => [
                        ['number' => '30', 'label' => ['sk' => 'Súťažiacich'], 'color' => 'primary', 'icon' => 'heroicon-o-users'],
                        ['number' => '3', 'label' => ['sk' => 'Kategórie'], 'color' => 'success', 'icon' => 'heroicon-o-trophy'],
                        ['number' => '5', 'label' => ['sk' => 'Disciplín'], 'color' => 'warning', 'icon' => 'heroicon-o-fire'],
                        ['number' => '5', 'label' => ['sk' => 'Rozhodcov'], 'color' => 'danger', 'icon' => 'heroicon-o-clipboard-document-check'],
                    ]],
                ]),
                'en' => self::masonBricks([
                    ['type' => 'heading', 'level' => 'h2', 'text' => ['en' => 'About the Competition']],
                    ['type' => 'rich-text', 'content' => ['en' => '<p>BCZ Live Cup 2026 is a full-day calisthenics competition combining a points-based qualification with battle finals. Three categories — men, women, and juniors — compete across five disciplines in front of a 5-judge panel.</p>']],
                    ['type' => 'heading', 'level' => 'h3', 'text' => ['en' => 'Scoring System']],
                    ['type' => 'rich-text', 'content' => ['en' => '<p><strong>Qualification:</strong> 90 seconds per competitor. Technique (40%), creativity (30%), difficulty (30%). Max 100 points.<br><strong>Battle finals:</strong> 1v1 elimination, 2x45 seconds. Judges vote by card — majority decides.</p>']],
                    ['type' => 'stats', 'badge' => ['en' => 'Today'], 'badge_color' => 'warning', 'title' => ['en' => 'Live Cup in Numbers'], 'items' => [
                        ['number' => '30', 'label' => ['en' => 'Competitors'], 'color' => 'primary', 'icon' => 'heroicon-o-users'],
                        ['number' => '3', 'label' => ['en' => 'Categories'], 'color' => 'success', 'icon' => 'heroicon-o-trophy'],
                        ['number' => '5', 'label' => ['en' => 'Disciplines'], 'color' => 'warning', 'icon' => 'heroicon-o-fire'],
                        ['number' => '5', 'label' => ['en' => 'Judges'], 'color' => 'danger', 'icon' => 'heroicon-o-clipboard-document-check'],
                    ]],
                ]),
            ],
        ]);
        $todayCompetition->addMediaFromUrl('https://picsum.photos/seed/event-livecup-card/800/450')
            ->toMediaCollection('card_image');
        $todayCompetition->addMediaFromUrl('https://picsum.photos/seed/event-livecup-hero/1440/500')
            ->toMediaCollection('detail_image');
        EventOrganization::factory()->paid(30.00)->create([
            'event_id' => $todayCompetition->id,
            'max_capacity' => 40,
            'registration_opens_at' => now()->subMonth(),
            'registration_closes_at' => now()->subDay(),
            'is_public_registration' => true,
        ]);
        $todayCompDetail = CompetitionDetail::factory()->create(['event_id' => $todayCompetition->id]);

        // Today's competition: Rounds (created BEFORE timetable so we can link them)
        // Men: Qualification
        $todayMenQual = CompetitionRound::factory()->create([
            'competition_detail_id' => $todayCompDetail->id,
            'athlete_category_id' => $menCategory->id,
            'round_number' => 1,
            'name' => 'Kvalifikácia - Muži',
            'scoring_format' => ScoringFormatEnum::POINTS,
            'advancement_type' => RoundAdvancementTypeEnum::TOP_BY_POINTS,
            'sort_order' => 1,
        ]);
        RoundPart::factory()->create([
            'competition_round_id' => $todayMenQual->id,
            'name' => ['sk' => 'Statika', 'en' => 'Statics', 'cs' => 'Statika'],
            'duration_seconds' => 90,
            'sort_order' => 1,
        ]);
        RoundPart::factory()->create([
            'competition_round_id' => $todayMenQual->id,
            'name' => ['sk' => 'Dynamika', 'en' => 'Dynamics', 'cs' => 'Dynamika'],
            'duration_seconds' => 90,
            'sort_order' => 2,
        ]);
        // Men: Battle Semifinals
        $todayMenSemifinal = CompetitionRound::factory()->create([
            'competition_detail_id' => $todayCompDetail->id,
            'athlete_category_id' => $menCategory->id,
            'round_number' => 2,
            'name' => 'Semifinále - Muži',
            'scoring_format' => ScoringFormatEnum::COACH_DECISION,
            'advancement_type' => RoundAdvancementTypeEnum::BATTLE_WINNER,
            'sort_order' => 2,
        ]);
        foreach (['Toprock', 'Footwork', 'Power Moves', 'Freeze'] as $i => $name) {
            RoundPart::factory()->create([
                'competition_round_id' => $todayMenSemifinal->id,
                'name' => ['sk' => $name, 'en' => $name, 'cs' => $name],
                'duration_seconds' => 45,
                'sort_order' => $i + 1,
            ]);
        }
        // Men: Battle Finals
        $todayMenBattle = CompetitionRound::factory()->create([
            'competition_detail_id' => $todayCompDetail->id,
            'athlete_category_id' => $menCategory->id,
            'round_number' => 3,
            'name' => 'Finále - Muži',
            'scoring_format' => ScoringFormatEnum::COACH_DECISION,
            'advancement_type' => RoundAdvancementTypeEnum::BATTLE_WINNER,
            'sort_order' => 3,
        ]);
        foreach (['Toprock', 'Footwork', 'Power Moves', 'Freeze'] as $i => $name) {
            RoundPart::factory()->create([
                'competition_round_id' => $todayMenBattle->id,
                'name' => ['sk' => $name, 'en' => $name, 'cs' => $name],
                'duration_seconds' => 45,
                'sort_order' => $i + 1,
            ]);
        }

        // Women: Qualification
        $todayWomenQual = CompetitionRound::factory()->create([
            'competition_detail_id' => $todayCompDetail->id,
            'athlete_category_id' => $womenCategory->id,
            'round_number' => 1,
            'name' => 'Kvalifikácia - Ženy',
            'scoring_format' => ScoringFormatEnum::POINTS,
            'advancement_type' => RoundAdvancementTypeEnum::TOP_BY_POINTS,
            'sort_order' => 4,
        ]);
        RoundPart::factory()->create([
            'competition_round_id' => $todayWomenQual->id,
            'name' => ['sk' => 'Statika', 'en' => 'Statics', 'cs' => 'Statika'],
            'duration_seconds' => 90,
            'sort_order' => 1,
        ]);
        RoundPart::factory()->create([
            'competition_round_id' => $todayWomenQual->id,
            'name' => ['sk' => 'Dynamika', 'en' => 'Dynamics', 'cs' => 'Dynamika'],
            'duration_seconds' => 90,
            'sort_order' => 2,
        ]);
        // Women: Battle Semifinals
        $todayWomenSemifinal = CompetitionRound::factory()->create([
            'competition_detail_id' => $todayCompDetail->id,
            'athlete_category_id' => $womenCategory->id,
            'round_number' => 2,
            'name' => 'Semifinále - Ženy',
            'scoring_format' => ScoringFormatEnum::COACH_DECISION,
            'advancement_type' => RoundAdvancementTypeEnum::BATTLE_WINNER,
            'sort_order' => 5,
        ]);
        foreach (['Toprock', 'Footwork', 'Flexibility', 'Musicality'] as $i => $name) {
            RoundPart::factory()->create([
                'competition_round_id' => $todayWomenSemifinal->id,
                'name' => ['sk' => $name, 'en' => $name, 'cs' => $name],
                'duration_seconds' => 45,
                'sort_order' => $i + 1,
            ]);
        }
        // Women: Battle Finals
        $todayWomenBattle = CompetitionRound::factory()->create([
            'competition_detail_id' => $todayCompDetail->id,
            'athlete_category_id' => $womenCategory->id,
            'round_number' => 3,
            'name' => 'Finále - Ženy',
            'scoring_format' => ScoringFormatEnum::COACH_DECISION,
            'advancement_type' => RoundAdvancementTypeEnum::BATTLE_WINNER,
            'sort_order' => 6,
        ]);
        foreach (['Toprock', 'Footwork', 'Flexibility', 'Musicality'] as $i => $name) {
            RoundPart::factory()->create([
                'competition_round_id' => $todayWomenBattle->id,
                'name' => ['sk' => $name, 'en' => $name, 'cs' => $name],
                'duration_seconds' => 45,
                'sort_order' => $i + 1,
            ]);
        }

        // Juniors: Qualification only (single round, no battle)
        $todayJuniorsQual = CompetitionRound::factory()->create([
            'competition_detail_id' => $todayCompDetail->id,
            'athlete_category_id' => $youthCategory->id,
            'round_number' => 1,
            'name' => 'Kvalifikácia - Juniori',
            'scoring_format' => ScoringFormatEnum::POINTS,
            'advancement_type' => RoundAdvancementTypeEnum::TOP_BY_POINTS,
            'sort_order' => 7,
        ]);
        RoundPart::factory()->create([
            'competition_round_id' => $todayJuniorsQual->id,
            'name' => ['sk' => 'Freestyle', 'en' => 'Freestyle', 'cs' => 'Freestyle'],
            'duration_seconds' => 60,
            'sort_order' => 1,
        ]);

        // Today's competition: Timetable with types and round links
        $todayTimetable = [
            ['title' => ['sk' => 'Registrácia a váženie', 'en' => 'Registration & Weigh-in', 'cs' => 'Registrace a vážení'], 'desc' => ['sk' => 'Overenie dokladov, váženie', 'en' => 'ID verification, weigh-in', 'cs' => 'Ověření dokladů, vážení'], 'time' => [8, 0], 'status' => TimetableEntryStatusEnum::IN_PROGRESS, 'type' => null, 'round_id' => null],
            ['title' => ['sk' => 'Otvorenie súťaže', 'en' => 'Opening Ceremony', 'cs' => 'Zahájení soutěže'], 'desc' => ['sk' => 'Privítanie, predstavenie rozhodcov', 'en' => 'Welcome, judges introduction', 'cs' => 'Přivítání, představení rozhodčích'], 'time' => [9, 30], 'status' => TimetableEntryStatusEnum::PENDING, 'type' => null, 'round_id' => null],
            ['title' => ['sk' => 'Kvalifikácia - Statika (Muži)', 'en' => 'Qualification - Statics (Men)', 'cs' => 'Kvalifikace - Statika (Muži)'], 'desc' => ['sk' => 'Statika, dynamika, kombá', 'en' => 'Statics, dynamics, combos', 'cs' => 'Statika, dynamika, komba'], 'time' => [10, 0], 'status' => TimetableEntryStatusEnum::PENDING, 'type' => TimetableEntryTypeEnum::COMPETITION_ROUND, 'round_id' => $todayMenQual->id],
            ['title' => ['sk' => 'Kvalifikácia - Statika (Ženy)', 'en' => 'Qualification - Statics (Women)', 'cs' => 'Kvalifikace - Statika (Ženy)'], 'desc' => ['sk' => 'Statika, dynamika, kombá', 'en' => 'Statics, dynamics, combos', 'cs' => 'Statika, dynamika, komba'], 'time' => [10, 45], 'status' => TimetableEntryStatusEnum::PENDING, 'type' => TimetableEntryTypeEnum::COMPETITION_ROUND, 'round_id' => $todayWomenQual->id],
            ['title' => ['sk' => 'Kvalifikácia - Dynamika (Muži)', 'en' => 'Qualification - Dynamics (Men)', 'cs' => 'Kvalifikace - Dynamika (Muži)'], 'desc' => ['sk' => 'Dynamické prvky, všetky kategórie', 'en' => 'Dynamic elements, all categories', 'cs' => 'Dynamické prvky, všechny kategorie'], 'time' => [11, 30], 'status' => TimetableEntryStatusEnum::PENDING, 'type' => TimetableEntryTypeEnum::COMPETITION_ROUND, 'round_id' => $todayMenQual->id],
            ['title' => ['sk' => 'Kvalifikácia - Dynamika (Ženy)', 'en' => 'Qualification - Dynamics (Women)', 'cs' => 'Kvalifikace - Dynamika (Ženy)'], 'desc' => ['sk' => 'Dynamické prvky, všetky kategórie', 'en' => 'Dynamic elements, all categories', 'cs' => 'Dynamické prvky, všechny kategorie'], 'time' => [12, 0], 'status' => TimetableEntryStatusEnum::PENDING, 'type' => TimetableEntryTypeEnum::COMPETITION_ROUND, 'round_id' => $todayWomenQual->id],
            ['title' => ['sk' => 'Obedová prestávka', 'en' => 'Lunch Break', 'cs' => 'Obědová přestávka'], 'desc' => null, 'time' => [12, 45], 'status' => TimetableEntryStatusEnum::PENDING, 'type' => null, 'round_id' => null],
            ['title' => ['sk' => 'Kvalifikácia - Juniori', 'en' => 'Qualification - Juniors', 'cs' => 'Kvalifikace - Junioři'], 'desc' => ['sk' => 'Záverečné kolo, všetky kategórie', 'en' => 'Final round, all categories', 'cs' => 'Závěrečné kolo, všechny kategorie'], 'time' => [13, 30], 'status' => TimetableEntryStatusEnum::PENDING, 'type' => TimetableEntryTypeEnum::COMPETITION_ROUND, 'round_id' => $todayJuniorsQual->id],
            ['title' => ['sk' => 'Battle semifinále (Muži)', 'en' => 'Battle Semi-finals (Men)', 'cs' => 'Battle semifinále (Muži)'], 'desc' => ['sk' => 'Top 4 z kvalifikácie, 1v1', 'en' => 'Top 4 from qualification, 1v1', 'cs' => 'Top 4 z kvalifikace, 1v1'], 'time' => [14, 30], 'status' => TimetableEntryStatusEnum::PENDING, 'type' => TimetableEntryTypeEnum::COMPETITION_ROUND, 'round_id' => $todayMenSemifinal->id],
            ['title' => ['sk' => 'Battle semifinále (Ženy)', 'en' => 'Battle Semi-finals (Women)', 'cs' => 'Battle semifinále (Ženy)'], 'desc' => ['sk' => 'Top 4 z kvalifikácie, 1v1', 'en' => 'Top 4 from qualification, 1v1', 'cs' => 'Top 4 z kvalifikace, 1v1'], 'time' => [15, 15], 'status' => TimetableEntryStatusEnum::PENDING, 'type' => TimetableEntryTypeEnum::COMPETITION_ROUND, 'round_id' => $todayWomenSemifinal->id],
            ['title' => ['sk' => 'Battle finále (Muži)', 'en' => 'Battle Finals (Men)', 'cs' => 'Battle finále (Muži)'], 'desc' => ['sk' => 'Finále 1v1', 'en' => 'Finals 1v1', 'cs' => 'Finále 1v1'], 'time' => [16, 0], 'status' => TimetableEntryStatusEnum::PENDING, 'type' => TimetableEntryTypeEnum::COMPETITION_ROUND, 'round_id' => $todayMenBattle->id],
            ['title' => ['sk' => 'Battle finále (Ženy)', 'en' => 'Battle Finals (Women)', 'cs' => 'Battle finále (Ženy)'], 'desc' => ['sk' => 'Finále 1v1', 'en' => 'Finals 1v1', 'cs' => 'Finále 1v1'], 'time' => [16, 30], 'status' => TimetableEntryStatusEnum::PENDING, 'type' => TimetableEntryTypeEnum::COMPETITION_ROUND, 'round_id' => $todayWomenBattle->id],
            ['title' => ['sk' => 'Vyhlásenie výsledkov', 'en' => 'Award Ceremony', 'cs' => 'Vyhlášení výsledků'], 'desc' => ['sk' => 'Odovzdávanie cien, spoločné foto', 'en' => 'Prize giving, group photo', 'cs' => 'Předávání cen, společné foto'], 'time' => [17, 30], 'status' => TimetableEntryStatusEnum::PENDING, 'type' => null, 'round_id' => null],
        ];
        foreach ($todayTimetable as $i => $entry) {
            TimetableEntry::factory()->create([
                'competition_detail_id' => $todayCompDetail->id,
                'title' => $entry['title'],
                'description' => $entry['desc'],
                'type' => $entry['type'],
                'competition_round_id' => $entry['round_id'],
                'scheduled_time' => today('Europe/Bratislava')->setTime(...$entry['time'])->utc(),
                'actual_start_time' => $i === 0 ? today('Europe/Bratislava')->setTime(8, 17)->utc() : null,
                'status' => $entry['status'],
                'sort_order' => $i,
            ]);
        }

        $allCompDetails = collect([$pastCompDetail, $battleCompDetail, $upcomingCompDetail, $freeCompDetail, $futureCompDetail, $todayCompDetail]);

        // Attach disciplines, categories, judges to all competitions (1 judge per discipline)
        $allCompDetails->each(function (CompetitionDetail $detail) use ($disciplines, $allCategories, $judges) {
            $attachedDisciplines = $disciplines->random(rand(3, 5));
            $detail->disciplines()->attach($attachedDisciplines->pluck('id'));
            $detail->athleteCategories()->attach($allCategories->pluck('id'));

            // Assign one judge per discipline (round-robin through available judges)
            $attachedDisciplines->values()->each(function (Discipline $disc, $index) use ($detail, $judges) {
                $judge = $judges[$index % $judges->count()];
                $detail->judges()->attach($judge->id, [
                    'discipline_id' => $disc->id,
                ]);
            });
        });

        // Registration fees — different pricing per competition
        $allCompDetails->each(function (CompetitionDetail $detail, $i) use ($allCategories) {
            $basePrice = [25.00, 15.00, 25.00, 0.00, 40.00, 30.00][$i] ?? 20.00;

            RegistrationFee::factory()->create([
                'competition_detail_id' => $detail->id,
                'amount' => $basePrice,
                'currency' => 'EUR',
                'description' => 'Standardny poplatok',
            ]);
            RegistrationFee::factory()->create([
                'competition_detail_id' => $detail->id,
                'athlete_category_id' => $allCategories->last()->id, // Juniori
                'amount' => max(0, $basePrice * 0.6),
                'currency' => 'EUR',
                'description' => 'Juniorsky zlavneny poplatok',
            ]);
        });

        // ===== TIMETABLES =====

        // Past competition (BCZ Championship) — all finished
        $pastTimetable = [
            ['title' => ['sk' => 'Registracia a vazenieí', 'en' => 'Registration & Weigh-in'], 'time' => [8, 0], 'status' => TimetableEntryStatusEnum::FINISHED],
            ['title' => ['sk' => 'Otvorenie sutaze', 'en' => 'Opening Ceremony'], 'time' => [9, 0], 'status' => TimetableEntryStatusEnum::FINISHED],
            ['title' => ['sk' => 'Statika - Kvalifikacia (Muzi)', 'en' => 'Statics - Qualification (Men)'], 'time' => [9, 30], 'status' => TimetableEntryStatusEnum::FINISHED],
            ['title' => ['sk' => 'Statika - Kvalifikacia (Zeny)', 'en' => 'Statics - Qualification (Women)'], 'time' => [10, 30], 'status' => TimetableEntryStatusEnum::FINISHED],
            ['title' => ['sk' => 'Dynamika - Kvalifikacia (Muzi)', 'en' => 'Dynamics - Qualification (Men)'], 'time' => [11, 30], 'status' => TimetableEntryStatusEnum::FINISHED],
            ['title' => ['sk' => 'Obedova prestavka', 'en' => 'Lunch Break'], 'time' => [12, 30], 'status' => TimetableEntryStatusEnum::FINISHED],
            ['title' => ['sk' => 'Kombinacie - Finale (Muzi)', 'en' => 'Combos - Finals (Men)'], 'time' => [14, 0], 'status' => TimetableEntryStatusEnum::FINISHED],
            ['title' => ['sk' => 'Freestyle - Finale (Zeny)', 'en' => 'Freestyle - Finals (Women)'], 'time' => [15, 0], 'status' => TimetableEntryStatusEnum::FINISHED],
            ['title' => ['sk' => 'Juniori - Finale', 'en' => 'Juniors - Finals'], 'time' => [16, 0], 'status' => TimetableEntryStatusEnum::FINISHED],
            ['title' => ['sk' => 'Vyhlasenie vysledkov', 'en' => 'Award Ceremony'], 'time' => [17, 0], 'status' => TimetableEntryStatusEnum::FINISHED],
        ];
        foreach ($pastTimetable as $i => $entry) {
            TimetableEntry::factory()->create([
                'competition_detail_id' => $pastCompDetail->id,
                'title' => $entry['title'],
                'scheduled_time' => $pastCompetition->date->copy()->setTime(...$entry['time']),
                'actual_start_time' => $pastCompetition->date->copy()->setTime($entry['time'][0], $entry['time'][1] + rand(0, 5)),
                'actual_end_time' => $pastCompetition->date->copy()->setTime($entry['time'][0] + 1, rand(0, 30)),
                'status' => $entry['status'],
                'sort_order' => $i,
            ]);
        }

        // Battle competition (Street Workout Battle) — all finished
        $battleTimetable = [
            ['title' => ['sk' => 'Registracia', 'en' => 'Registration'], 'time' => [9, 0], 'status' => TimetableEntryStatusEnum::FINISHED],
            ['title' => ['sk' => 'Rozohriatie', 'en' => 'Warm-up'], 'time' => [10, 0], 'status' => TimetableEntryStatusEnum::FINISHED],
            ['title' => ['sk' => 'Osminove kolo', 'en' => 'Round of 16'], 'time' => [10, 30], 'status' => TimetableEntryStatusEnum::FINISHED],
            ['title' => ['sk' => 'Stvrfinale', 'en' => 'Quarter-finals'], 'time' => [12, 0], 'status' => TimetableEntryStatusEnum::FINISHED],
            ['title' => ['sk' => 'Semifinale', 'en' => 'Semi-finals'], 'time' => [14, 0], 'status' => TimetableEntryStatusEnum::FINISHED],
            ['title' => ['sk' => 'Battle o 3. miesto', 'en' => '3rd Place Battle'], 'time' => [15, 30], 'status' => TimetableEntryStatusEnum::FINISHED],
            ['title' => ['sk' => 'Finale', 'en' => 'Finals'], 'time' => [16, 0], 'status' => TimetableEntryStatusEnum::FINISHED],
            ['title' => ['sk' => 'Vyhlasenie a afterparty', 'en' => 'Awards & Afterparty'], 'time' => [17, 0], 'status' => TimetableEntryStatusEnum::FINISHED],
        ];
        foreach ($battleTimetable as $i => $entry) {
            TimetableEntry::factory()->create([
                'competition_detail_id' => $battleCompDetail->id,
                'title' => $entry['title'],
                'scheduled_time' => $battleCompetition->date->copy()->setTime(...$entry['time']),
                'status' => $entry['status'],
                'sort_order' => $i,
            ]);
        }

        // Upcoming competition — all pending
        $upcomingTimetable = [
            ['title' => ['sk' => 'Registracia a vazenie', 'en' => 'Registration & Weigh-in'], 'time' => [8, 0]],
            ['title' => ['sk' => 'Otvorenie', 'en' => 'Opening Ceremony'], 'time' => [9, 0]],
            ['title' => ['sk' => 'Kvalifikacia - Statika', 'en' => 'Qualification - Statics'], 'time' => [9, 30]],
            ['title' => ['sk' => 'Kvalifikacia - Dynamika', 'en' => 'Qualification - Dynamics'], 'time' => [11, 0]],
            ['title' => ['sk' => 'Obedova prestavka', 'en' => 'Lunch Break'], 'time' => [12, 30]],
            ['title' => ['sk' => 'Finale - Kombinacie', 'en' => 'Finals - Combos'], 'time' => [14, 0]],
            ['title' => ['sk' => 'Battle Finale', 'en' => 'Battle Finals'], 'time' => [15, 30]],
            ['title' => ['sk' => 'Vyhlasenie vysledkov', 'en' => 'Award Ceremony'], 'time' => [17, 0]],
        ];
        foreach ($upcomingTimetable as $i => $entry) {
            TimetableEntry::factory()->create([
                'competition_detail_id' => $upcomingCompDetail->id,
                'title' => $entry['title'],
                'scheduled_time' => $upcomingCompetition->date->copy()->setTime(...$entry['time']),
                'status' => TimetableEntryStatusEnum::PENDING,
                'sort_order' => $i,
            ]);
        }

        // Community Jam — simple timetable, all finished
        $jamTimetable = [
            ['title' => ['sk' => 'Zraz', 'en' => 'Gathering'], 'time' => [10, 0], 'status' => TimetableEntryStatusEnum::FINISHED],
            ['title' => ['sk' => 'Spolocne rozohriatie', 'en' => 'Group Warm-up'], 'time' => [10, 30], 'status' => TimetableEntryStatusEnum::FINISHED],
            ['title' => ['sk' => 'Freestyle kola', 'en' => 'Freestyle Rounds'], 'time' => [11, 0], 'status' => TimetableEntryStatusEnum::FINISHED],
            ['title' => ['sk' => 'Vyhlasenie a grilovacka', 'en' => 'Awards & BBQ'], 'time' => [14, 0], 'status' => TimetableEntryStatusEnum::FINISHED],
        ];
        foreach ($jamTimetable as $i => $entry) {
            TimetableEntry::factory()->create([
                'competition_detail_id' => $freeCompDetail->id,
                'title' => $entry['title'],
                'scheduled_time' => $freeCompetition->date->copy()->setTime(...$entry['time']),
                'status' => $entry['status'],
                'sort_order' => $i,
            ]);
        }

        // ===== COMPETITION REGISTRATIONS =====

        // Create extra athletes for competitions to have realistic numbers
        $compAthletes = User::factory(12)->create()->each(function (User $user) use ($bczTeam) {
            $user->assignRole(RoleEnum::CUSTOMER);
            $user->teams()->attach($bczTeam, ['role' => RoleEnum::ATHLETE->value, 'is_active' => true, 'joined_at' => now()->subMonths(rand(1, 12))]);
        });
        $allCompetitors = $athletes->merge($compAthletes);

        // Past championship — all confirmed, 20 athletes across categories
        $allCompetitors->take(20)->each(function (User $athlete, $index) use ($pastCompetition, $allCategories) {
            $category = $allCategories[$index % 3];
            EventRegistration::factory()->create([
                'event_id' => $pastCompetition->id,
                'user_id' => $athlete->id,
                'athlete_category_id' => $category->id,
                'status' => 'approved',
                'weight_in' => rand(55, 95) + (rand(0, 9) / 10),
                'registered_at' => now()->subMonths(4)->addDays($index),
            ]);
        });

        // Battle competition — 16 athletes, men only
        $allCompetitors->take(16)->each(function (User $athlete, $index) use ($battleCompetition, $menCategory) {
            EventRegistration::factory()->create([
                'event_id' => $battleCompetition->id,
                'user_id' => $athlete->id,
                'athlete_category_id' => $menCategory->id,
                'status' => 'approved',
                'weight_in' => rand(60, 90) + (rand(0, 9) / 10),
                'registered_at' => now()->subMonths(2)->addDays($index),
            ]);
        });

        // Upcoming — mixed confirmed/pending
        $allCompetitors->take(15)->each(function (User $athlete, $index) use ($upcomingCompetition, $allCategories) {
            $category = $allCategories[$index % 3];
            EventRegistration::factory()->create([
                'event_id' => $upcomingCompetition->id,
                'user_id' => $athlete->id,
                'athlete_category_id' => $category->id,
                'status' => $index < 12 ? 'approved' : 'pending',
                'registered_at' => now()->subDays(7 - ($index % 7)),
            ]);
        });

        // Community jam — small group
        $allCompetitors->take(12)->each(function (User $athlete, $index) use ($freeCompetition, $allCategories) {
            EventRegistration::factory()->create([
                'event_id' => $freeCompetition->id,
                'user_id' => $athlete->id,
                'athlete_category_id' => $allCategories[$index % 3]->id,
                'status' => 'approved',
                'registered_at' => now()->subMonths(6)->addDays($index),
            ]);
        });

        // Today's Live Cup — all approved, NO weight_in (weigh-in in progress)
        $allCompetitors->each(function (User $athlete, $index) use ($todayCompetition, $allCategories) {
            if ($index >= 20) {
                return;
            }
            $category = $allCategories[$index % 3];
            EventRegistration::factory()->create([
                'event_id' => $todayCompetition->id,
                'user_id' => $athlete->id,
                'athlete_category_id' => $category->id,
                'status' => 'approved',
                'weight_in' => null,
                'registered_at' => now()->subDays(rand(3, 25)),
            ]);
        });

        // Today's Live Cup — seed battles for SEMIFINÁLE rounds (top 4 men, top 4 women)
        // Finále rounds stay empty (placeholder brackets in public view)
        $todayMenReg = EventRegistration::where('event_id', $todayCompetition->id)
            ->where('athlete_category_id', $menCategory->id)
            ->with('user')
            ->orderBy('registered_at')
            ->take(4)
            ->get();
        if ($todayMenReg->count() >= 4) {
            Battle::factory()
                ->pair($todayMenReg[0]->user, $todayMenReg[3]->user)
                ->create([
                    'competition_round_id' => $todayMenSemifinal->id,
                    'athlete_category_id' => $menCategory->id,
                    'bracket_position' => 1,
                ]);
            Battle::factory()
                ->pair($todayMenReg[1]->user, $todayMenReg[2]->user)
                ->create([
                    'competition_round_id' => $todayMenSemifinal->id,
                    'athlete_category_id' => $menCategory->id,
                    'bracket_position' => 2,
                ]);
        }

        $todayWomenReg = EventRegistration::where('event_id', $todayCompetition->id)
            ->where('athlete_category_id', $womenCategory->id)
            ->with('user')
            ->orderBy('registered_at')
            ->take(4)
            ->get();
        if ($todayWomenReg->count() >= 4) {
            Battle::factory()
                ->pair($todayWomenReg[0]->user, $todayWomenReg[3]->user)
                ->create([
                    'competition_round_id' => $todayWomenSemifinal->id,
                    'athlete_category_id' => $womenCategory->id,
                    'bracket_position' => 1,
                ]);
            Battle::factory()
                ->pair($todayWomenReg[1]->user, $todayWomenReg[2]->user)
                ->create([
                    'competition_round_id' => $todayWomenSemifinal->id,
                    'athlete_category_id' => $womenCategory->id,
                    'bracket_position' => 2,
                ]);
        }

        // Spring Cup — seed battles for Battle finále (top 4 men)
        $springMenReg = EventRegistration::where('event_id', $upcomingCompetition->id)
            ->where('athlete_category_id', $menCategory->id)
            ->where('status', 'approved')
            ->with('user')
            ->orderBy('registered_at')
            ->take(4)
            ->get();
        if ($springMenReg->count() >= 4) {
            $springBattleRound = CompetitionRound::where('competition_detail_id', $upcomingCompDetail->id)
                ->where('advancement_type', RoundAdvancementTypeEnum::BATTLE_WINNER)
                ->first();
            if ($springBattleRound) {
                Battle::factory()
                    ->pair($springMenReg[0]->user, $springMenReg[3]->user)
                    ->create([
                        'competition_round_id' => $springBattleRound->id,
                        'athlete_category_id' => $menCategory->id,
                        'bracket_position' => 1,
                    ]);
                Battle::factory()
                    ->pair($springMenReg[1]->user, $springMenReg[2]->user)
                    ->create([
                        'competition_round_id' => $springBattleRound->id,
                        'athlete_category_id' => $menCategory->id,
                        'bracket_position' => 2,
                    ]);
            }
        }

        // ===== COMPETITION 1: BCZ Championship — POINTS-BASED ROUNDS =====
        // Men: Qualification (Statics + Dynamics) → Final (Combos)
        $menQual = CompetitionRound::factory()->create([
            'competition_detail_id' => $pastCompDetail->id,
            'athlete_category_id' => $menCategory->id,
            'round_number' => 1,
            'name' => 'Kvalifikacia - Muzi',
            'scoring_format' => 'points',
            'advancement_type' => RoundAdvancementTypeEnum::TOP_BY_POINTS,
            'sort_order' => 1,
        ]);
        $menFinal = CompetitionRound::factory()->create([
            'competition_detail_id' => $pastCompDetail->id,
            'athlete_category_id' => $menCategory->id,
            'round_number' => 2,
            'name' => 'Finale - Muzi',
            'scoring_format' => 'points',
            'advancement_type' => RoundAdvancementTypeEnum::TOP_BY_POINTS,
            'sort_order' => 2,
        ]);

        // Women: Qualification → Final
        $womenQual = CompetitionRound::factory()->create([
            'competition_detail_id' => $pastCompDetail->id,
            'athlete_category_id' => $womenCategory->id,
            'round_number' => 1,
            'name' => 'Kvalifikacia - Zeny',
            'scoring_format' => 'points',
            'advancement_type' => RoundAdvancementTypeEnum::TOP_BY_POINTS,
            'sort_order' => 3,
        ]);
        $womenFinal = CompetitionRound::factory()->create([
            'competition_detail_id' => $pastCompDetail->id,
            'athlete_category_id' => $womenCategory->id,
            'round_number' => 2,
            'name' => 'Finale - Zeny',
            'scoring_format' => 'points',
            'advancement_type' => RoundAdvancementTypeEnum::TOP_BY_POINTS,
            'sort_order' => 4,
        ]);

        // Juniors: Single round
        $juniorsRound = CompetitionRound::factory()->create([
            'competition_detail_id' => $pastCompDetail->id,
            'athlete_category_id' => $youthCategory->id,
            'round_number' => 1,
            'name' => 'Juniori - Finale',
            'scoring_format' => 'points',
            'advancement_type' => RoundAdvancementTypeEnum::TOP_BY_POINTS,
            'sort_order' => 5,
        ]);

        // Round parts and results for Men Qualification
        $menQualStatics = RoundPart::factory()->create([
            'competition_round_id' => $menQual->id,
            'name' => ['sk' => 'Statika', 'en' => 'Statics'],
            'duration_seconds' => 30,
            'sort_order' => 1,
        ]);
        $menQualDynamics = RoundPart::factory()->create([
            'competition_round_id' => $menQual->id,
            'name' => ['sk' => 'Dynamika', 'en' => 'Dynamics'],
            'duration_seconds' => 45,
            'sort_order' => 2,
        ]);

        // Men Qual results — 8 competitors, scored and ranked (shuffled for varied placements)
        $menCompetitors = $allCompetitors->take(8)->shuffle()->values();
        $menScores = collect(range(0, 7))->map(fn ($i) => [
            'statics' => round(95 - ($i * 4.5) + (rand(-15, 15) / 10), 2),
            'dynamics' => round(92 - ($i * 3.8) + (rand(-20, 20) / 10), 2),
        ])->sortByDesc(fn ($s) => $s['statics'] + $s['dynamics'])->values();

        $menCompetitors->each(function (User $athlete, $index) use ($menQualStatics, $menQualDynamics, $menScores) {
            CompetitionResult::factory()->create([
                'round_part_id' => $menQualStatics->id,
                'user_id' => $athlete->id,
                'score' => $menScores[$index]['statics'],
                'place' => $index + 1,
            ]);
            CompetitionResult::factory()->create([
                'round_part_id' => $menQualDynamics->id,
                'user_id' => $athlete->id,
                'score' => $menScores[$index]['dynamics'],
                'place' => $index + 1,
            ]);
        });

        // Men Final — top 6 compete in combos
        $menFinalCombos = RoundPart::factory()->create([
            'competition_round_id' => $menFinal->id,
            'name' => ['sk' => 'Kombinacie', 'en' => 'Combos'],
            'duration_seconds' => 60,
            'sort_order' => 1,
        ]);
        $menCompetitors->take(6)->each(function (User $athlete, $index) use ($menFinalCombos) {
            CompetitionResult::factory()->create([
                'round_part_id' => $menFinalCombos->id,
                'user_id' => $athlete->id,
                'score' => round(97 - ($index * 5.2) + (rand(-10, 10) / 10), 2),
                'place' => $index + 1,
            ]);
        });

        // Women Qualification results — 6 competitors
        $womenQualFreestyle = RoundPart::factory()->create([
            'competition_round_id' => $womenQual->id,
            'name' => ['sk' => 'Freestyle', 'en' => 'Freestyle'],
            'duration_seconds' => 45,
            'sort_order' => 1,
        ]);
        $womenCompetitors = $allCompetitors->slice(8, 6)->values();
        $womenCompetitors->each(function (User $athlete, $index) use ($womenQualFreestyle) {
            CompetitionResult::factory()->create([
                'round_part_id' => $womenQualFreestyle->id,
                'user_id' => $athlete->id,
                'score' => round(90 - ($index * 5) + (rand(-15, 15) / 10), 2),
                'place' => $index + 1,
            ]);
        });

        // Women Final — top 4
        $womenFinalFreestyle = RoundPart::factory()->create([
            'competition_round_id' => $womenFinal->id,
            'name' => ['sk' => 'Freestyle Finale', 'en' => 'Freestyle Finals'],
            'duration_seconds' => 60,
            'sort_order' => 1,
        ]);
        $womenCompetitors->take(4)->each(function (User $athlete, $index) use ($womenFinalFreestyle) {
            CompetitionResult::factory()->create([
                'round_part_id' => $womenFinalFreestyle->id,
                'user_id' => $athlete->id,
                'score' => round(94 - ($index * 6) + (rand(-10, 10) / 10), 2),
                'place' => $index + 1,
            ]);
        });

        // Juniors — single round, 6 competitors
        $juniorsFreestyle = RoundPart::factory()->create([
            'competition_round_id' => $juniorsRound->id,
            'name' => ['sk' => 'Freestyle Juniori', 'en' => 'Juniors Freestyle'],
            'duration_seconds' => 40,
            'sort_order' => 1,
        ]);
        $juniorCompetitors = $allCompetitors->slice(14, 6)->values();
        $juniorCompetitors->each(function (User $athlete, $index) use ($juniorsFreestyle) {
            CompetitionResult::factory()->create([
                'round_part_id' => $juniorsFreestyle->id,
                'user_id' => $athlete->id,
                'score' => round(85 - ($index * 4) + (rand(-10, 10) / 10), 2),
                'place' => $index + 1,
            ]);
        });

        // ===== COMPETITION 2: Street Workout Battle — FULL BRACKET SYSTEM =====
        $battleAthletes = $allCompetitors->take(16);

        // Round of 16 (8 battles)
        $roundOf16 = CompetitionRound::factory()->create([
            'competition_detail_id' => $battleCompDetail->id,
            'athlete_category_id' => $menCategory->id,
            'round_number' => 1,
            'name' => 'Osminove kolo',
            'scoring_format' => 'coach_decision',
            'advancement_type' => RoundAdvancementTypeEnum::BATTLE_WINNER,
            'sort_order' => 1,
        ]);
        $r16Winners = collect();
        for ($b = 0; $b < 8; $b++) {
            $a = $battleAthletes[$b * 2];
            $bComp = $battleAthletes[$b * 2 + 1];
            $winner = rand(0, 1) === 0 ? $a : $bComp;
            $r16Winners->push($winner);
            Battle::factory()
                ->pair($a, $bComp, $winner)
                ->create([
                    'competition_round_id' => $roundOf16->id,
                    'athlete_category_id' => $menCategory->id,
                    'bracket_position' => $b + 1,
                ]);
        }

        // Quarter-finals (4 battles)
        $quarterFinals = CompetitionRound::factory()->create([
            'competition_detail_id' => $battleCompDetail->id,
            'athlete_category_id' => $menCategory->id,
            'round_number' => 2,
            'name' => 'Stvrfinale',
            'scoring_format' => 'coach_decision',
            'advancement_type' => RoundAdvancementTypeEnum::BATTLE_WINNER,
            'sort_order' => 2,
        ]);
        $qfWinners = collect();
        for ($b = 0; $b < 4; $b++) {
            $a = $r16Winners[$b * 2];
            $bComp = $r16Winners[$b * 2 + 1];
            $winner = rand(0, 1) === 0 ? $a : $bComp;
            $qfWinners->push($winner);
            Battle::factory()
                ->pair($a, $bComp, $winner)
                ->create([
                    'competition_round_id' => $quarterFinals->id,
                    'athlete_category_id' => $menCategory->id,
                    'bracket_position' => $b + 1,
                ]);
        }

        // Semi-finals (2 battles)
        $semiFinals = CompetitionRound::factory()->create([
            'competition_detail_id' => $battleCompDetail->id,
            'athlete_category_id' => $menCategory->id,
            'round_number' => 3,
            'name' => 'Semifinale',
            'scoring_format' => 'coach_decision',
            'advancement_type' => RoundAdvancementTypeEnum::BATTLE_WINNER,
            'sort_order' => 3,
        ]);
        $sfWinners = collect();
        $sfLosers = collect();
        for ($b = 0; $b < 2; $b++) {
            $a = $qfWinners[$b * 2];
            $bComp = $qfWinners[$b * 2 + 1];
            $winner = rand(0, 1) === 0 ? $a : $bComp;
            $loser = $winner->id === $a->id ? $bComp : $a;
            $sfWinners->push($winner);
            $sfLosers->push($loser);
            Battle::factory()
                ->pair($a, $bComp, $winner)
                ->create([
                    'competition_round_id' => $semiFinals->id,
                    'athlete_category_id' => $menCategory->id,
                    'bracket_position' => $b + 1,
                ]);
        }

        // 3rd place battle
        $thirdPlaceRound = CompetitionRound::factory()->create([
            'competition_detail_id' => $battleCompDetail->id,
            'athlete_category_id' => $menCategory->id,
            'round_number' => 4,
            'name' => 'Battle o 3. miesto',
            'scoring_format' => 'coach_decision',
            'advancement_type' => RoundAdvancementTypeEnum::BATTLE_WINNER,
            'sort_order' => 4,
        ]);
        $thirdPlaceWinner = $sfLosers->random();
        Battle::factory()
            ->pair($sfLosers[0], $sfLosers[1], $thirdPlaceWinner)
            ->create([
                'competition_round_id' => $thirdPlaceRound->id,
                'athlete_category_id' => $menCategory->id,
                'bracket_position' => 1,
            ]);

        // Grand Final
        $grandFinal = CompetitionRound::factory()->create([
            'competition_detail_id' => $battleCompDetail->id,
            'athlete_category_id' => $menCategory->id,
            'round_number' => 5,
            'name' => 'Finale',
            'scoring_format' => 'coach_decision',
            'advancement_type' => RoundAdvancementTypeEnum::BATTLE_WINNER,
            'sort_order' => 5,
        ]);
        $champion = $sfWinners->random();
        Battle::factory()
            ->pair($sfWinners[0], $sfWinners[1], $champion)
            ->create([
                'competition_round_id' => $grandFinal->id,
                'athlete_category_id' => $menCategory->id,
                'bracket_position' => 1,
            ]);

        // ===== COMPETITION 4: Community Jam — simple points, no battles =====
        $jamRound = CompetitionRound::factory()->create([
            'competition_detail_id' => $freeCompDetail->id,
            'round_number' => 1,
            'name' => 'Freestyle Jam',
            'scoring_format' => 'points',
            'advancement_type' => RoundAdvancementTypeEnum::TOP_BY_POINTS,
            'sort_order' => 1,
        ]);
        $jamPart = RoundPart::factory()->create([
            'competition_round_id' => $jamRound->id,
            'name' => ['sk' => 'Freestyle', 'en' => 'Freestyle'],
            'duration_seconds' => 60,
            'sort_order' => 1,
        ]);
        $allCompetitors->take(12)->shuffle()->values()->each(function (User $athlete, $index) use ($jamPart) {
            CompetitionResult::factory()->create([
                'round_part_id' => $jamPart->id,
                'user_id' => $athlete->id,
                'score' => round(80 - ($index * 3.5) + (rand(-20, 20) / 10), 2),
                'place' => $index + 1,
            ]);
        });

        // --- Phase 5: Inquiries ---
        Inquiry::factory()->create([
            'team_id' => $bczTeam->id,
            'name' => 'Peter Novák',
            'email' => 'peter.novak@example.com',
            'reason' => InquiryReasonEnum::TRAINING,
            'message' => 'Dobrý deň, mal by som záujem o tréningy parkour pre začiatočníkov. Aký je rozpis a cena?',
            'status' => InquiryStatusEnum::NEW,
        ]);
        Inquiry::factory()->create([
            'team_id' => $bczTeam->id,
            'name' => 'Jana Kováčová',
            'email' => 'jana.k@example.com',
            'reason' => InquiryReasonEnum::EXHIBITION,
            'message' => 'Chceli by sme objednať vystúpenie na firemný teambuilding 15. mája. Koľko to stojí?',
            'status' => InquiryStatusEnum::IN_PROGRESS,
        ]);
        Inquiry::factory()->create([
            'team_id' => $bczTeam->id,
            'name' => 'Martin Horváth',
            'email' => 'martin.h@example.com',
            'reason' => InquiryReasonEnum::COMPETITION,
            'message' => 'Ako sa môžem prihlásiť na BCZ Spring Cup 2026? Ďakujem.',
            'status' => InquiryStatusEnum::RESOLVED,
        ]);
        Inquiry::factory(4)->create([
            'team_id' => $bczTeam->id,
            'status' => InquiryStatusEnum::NEW,
        ]);
        Inquiry::factory(2)->create([
            'team_id' => $secondTeam->id,
            'status' => InquiryStatusEnum::NEW,
        ]);

        // --- Phase 5: FAQ Categories & FAQs ---
        $faqGeneral = FaqCategory::factory()->create([
            'title' => ['sk' => 'Všeobecné', 'en' => 'General', 'cz' => 'Obecné'],
            'color' => '#3B82F6',
            'icon' => 'heroicon-o-question-mark-circle',
            'sort_order' => 1,
        ]);
        $faqTraining = FaqCategory::factory()->create([
            'title' => ['sk' => 'Tréningy', 'en' => 'Trainings', 'cz' => 'Tréninky'],
            'color' => '#10B981',
            'icon' => 'heroicon-o-academic-cap',
            'sort_order' => 2,
        ]);
        $faqCompetition = FaqCategory::factory()->create([
            'title' => ['sk' => 'Súťaže', 'en' => 'Competitions', 'cz' => 'Soutěže'],
            'color' => '#F59E0B',
            'icon' => 'heroicon-o-trophy',
            'sort_order' => 3,
        ]);

        Faq::factory()->create([
            'faq_category_id' => $faqGeneral->id,
            'question' => ['sk' => 'Čo je BCZ Club?', 'en' => 'What is BCZ Club?'],
            'answer' => ['sk' => 'BCZ Club je slovenská organizácia zameraná na parkour, freerunning a kalisteniku.', 'en' => 'BCZ Club is a Slovak organization focused on parkour, freerunning and calisthenics.'],
            'sort_order' => 1,
        ]);
        Faq::factory()->create([
            'faq_category_id' => $faqGeneral->id,
            'question' => ['sk' => 'Pre koho sú aktivity určené?', 'en' => 'Who are the activities for?'],
            'answer' => ['sk' => 'Naše aktivity sú určené pre všetky vekové kategórie a úrovne pokročilosti.', 'en' => 'Our activities are designed for all age groups and skill levels.'],
            'sort_order' => 2,
        ]);
        Faq::factory()->create([
            'faq_category_id' => $faqTraining->id,
            'question' => ['sk' => 'Kde prebiehajú tréningy?', 'en' => 'Where do trainings take place?'],
            'answer' => ['sk' => 'Tréningy prebiehajú v rôznych lokalitách v Bratislave a Košiciach. Presné miesto je vždy uvedené pri konkrétnom tréningu.', 'en' => 'Trainings take place in various locations in Bratislava and Košice. The exact location is always listed with each specific training.'],
            'sort_order' => 1,
        ]);
        Faq::factory()->create([
            'faq_category_id' => $faqTraining->id,
            'question' => ['sk' => 'Koľko stojí tréning?', 'en' => 'How much does a training cost?'],
            'answer' => ['sk' => 'Ceny sa líšia podľa typu tréningu. Niektoré tréningy sú zadarmo, iné vyžadujú členstvo alebo jednorazový poplatok.', 'en' => 'Prices vary by training type. Some trainings are free, others require membership or a one-time fee.'],
            'sort_order' => 2,
        ]);
        Faq::factory()->create([
            'faq_category_id' => $faqCompetition->id,
            'question' => ['sk' => 'Ako sa prihlásim na súťaž?', 'en' => 'How do I register for a competition?'],
            'answer' => ['sk' => 'Registrácia prebieha cez náš web. Pri každej súťaži nájdete registračný formulár s detailmi o kategóriách a poplatkoch.', 'en' => 'Registration is done through our website. You will find a registration form with details about categories and fees for each competition.'],
            'sort_order' => 1,
        ]);

        // --- Phase 5: Sponsors ---
        $sponsorData = [
            ['name' => 'Red Bull', 'tag' => SponsorTagEnum::MAIN_SPONSOR, 'link' => 'https://www.redbull.com/sk-sk', 'is_visible' => true, 'sort_order' => 1],
            ['name' => 'Nike', 'tag' => SponsorTagEnum::MAIN_SPONSOR, 'link' => 'https://www.nike.com', 'is_visible' => true, 'sort_order' => 2],
            ['name' => 'Denník N', 'tag' => SponsorTagEnum::MEDIAL_SPONSOR, 'link' => 'https://dennikn.sk', 'is_visible' => true, 'sort_order' => 3],
            ['name' => 'Město Bratislava', 'tag' => SponsorTagEnum::PARTNER, 'link' => 'https://bratislava.sk', 'is_visible' => true, 'sort_order' => 4],
            ['name' => 'Decathlon', 'tag' => SponsorTagEnum::SUPPORTER, 'link' => 'https://www.decathlon.sk', 'is_visible' => true, 'sort_order' => 5],
            ['name' => 'GymBeam', 'tag' => SponsorTagEnum::SUPPORTER, 'link' => 'https://www.gymbeam.sk', 'is_visible' => false, 'sort_order' => 6],
        ];

        foreach ($sponsorData as $index => $data) {
            $sponsor = Sponsor::factory()->create($data);

            $sponsor->addMediaFromUrl("https://picsum.photos/seed/sponsor-{$index}/200/100")
                ->usingFileName('sponsor-'.Str::slug($data['name']).'.png')
                ->toMediaCollection('logo');
        }

        // --- Phase 5-6: Memberships, Payments, Subscriptions, Payouts ---

        // Enable membership on BCZ team
        $bczTeam->update([
            'membership_enabled' => true,
            'membership_fee_currency' => 'EUR',
            'membership_description' => 'Sezónne členstvo v BCZ Club zahŕňa prístup k tréningom a zľavy na súťaže.',
            'bank_account_iban' => 'SK89 7500 0000 0000 1234 5678',
            'bank_account_name' => 'BCZ Club o.z.',
        ]);

        // Create seasons for BCZ team — historical + current + future
        $olderSeason = TeamSeason::create([
            'team_id' => $bczTeam->id,
            'name' => 'Sezóna '.(now()->year - 2),
            'starts_at' => now()->subYears(2)->startOfYear()->month(3)->startOfMonth(),
            'ends_at' => now()->subYears(2)->startOfYear()->month(11)->endOfMonth(),
            'fee_amount' => 40.00,
            'fee_currency' => 'EUR',
            'payment_deadline_days' => 14,
        ]);

        $pastSeason = TeamSeason::create([
            'team_id' => $bczTeam->id,
            'name' => 'Sezóna '.(now()->year - 1),
            'starts_at' => now()->subYear()->startOfYear()->month(3)->startOfMonth(),
            'ends_at' => now()->subYear()->startOfYear()->month(11)->endOfMonth(),
            'fee_amount' => 45.00,
            'fee_currency' => 'EUR',
            'payment_deadline_days' => 14,
        ]);

        $currentSeason = TeamSeason::create([
            'team_id' => $bczTeam->id,
            'name' => 'Sezóna '.now()->year,
            'starts_at' => now()->startOfYear()->month(3)->startOfMonth(),
            'ends_at' => now()->startOfYear()->month(11)->endOfMonth(),
            'fee_amount' => 50.00,
            'fee_currency' => 'EUR',
            'max_capacity' => 20,
            'payment_deadline_days' => 14,
        ]);

        $futureSeason = TeamSeason::create([
            'team_id' => $bczTeam->id,
            'name' => 'Sezóna '.(now()->year + 1),
            'starts_at' => now()->addYear()->startOfYear()->month(3)->startOfMonth(),
            'ends_at' => now()->addYear()->startOfYear()->month(11)->endOfMonth(),
            'fee_amount' => 55.00,
            'fee_currency' => 'EUR',
            'payment_deadline_days' => 21,
        ]);

        // Link current trainings to current season
        Training::where('team_id', $bczTeam->id)->whereNull('team_season_id')->update(['team_season_id' => $currentSeason->id]);

        // Create historical trainings for past season (to demonstrate history)
        $historicalTrainings = collect([
            ['title' => ['sk' => 'Parkour Zaklady 2024', 'en' => 'Parkour Basics 2024'], 'sport_category_id' => $parkour->id],
            ['title' => ['sk' => 'Street Workout Leto 2024', 'en' => 'Street Workout Summer 2024'], 'sport_category_id' => $streetWorkout->id],
            ['title' => ['sk' => 'Freerunning Camp 2024', 'en' => 'Freerunning Camp 2024'], 'sport_category_id' => $parkour->id],
        ]);

        $historicalTrainings->each(function ($data, $index) use ($bczTeam, $pastSeason, $cadca, $coaches, $athletes) {
            $training = Training::factory()->create(array_merge($data, [
                'team_id' => $bczTeam->id,
                'team_season_id' => $pastSeason->id,
                'city_id' => $cadca->id,
                'pricing_type' => TrainingPricingTypeEnum::MEMBERSHIP_REQUIRED,
                'is_active' => true,
                'is_recurring_across_seasons' => $index < 2,
                'max_capacity' => 15,
                'sort_order' => $index,
            ]));

            $training->coaches()->attach($coaches[$index % 3]->id, ['role' => CoachRoleEnum::MAIN->value]);

            // Add historical registrations (all approved — these are past)
            $athletes->take(rand(5, 10))->each(function (User $athlete) use ($training) {
                TrainingRegistration::factory()->forTraining($training)->create([
                    'user_id' => $athlete->id,
                    'status' => RegistrationStatusEnum::Approved,
                    'registered_at' => $training->season->starts_at->addDays(rand(1, 30)),
                ]);
            });
        });

        // Historical memberships for older season (all expired/completed)
        $athletes->take(4)->each(function (User $athlete) use ($bczTeam, $olderSeason) {
            Membership::create([
                'team_id' => $bczTeam->id,
                'user_id' => $athlete->id,
                'team_season_id' => $olderSeason->id,
                'status' => MembershipStatusEnum::COMPLETED,
                'fee_amount' => 40.00,
                'fee_currency' => 'EUR',
                'starts_at' => $olderSeason->starts_at,
                'ends_at' => $olderSeason->ends_at,
            ]);
        });

        // Past season memberships — mix of expired and cancelled
        $athletes->take(6)->each(function (User $athlete, $index) use ($bczTeam, $pastSeason) {
            $status = $index < 4 ? MembershipStatusEnum::COMPLETED : MembershipStatusEnum::CANCELLED;

            Membership::create([
                'team_id' => $bczTeam->id,
                'user_id' => $athlete->id,
                'team_season_id' => $pastSeason->id,
                'status' => $status,
                'fee_amount' => $status === MembershipStatusEnum::CANCELLED ? 0 : 45.00,
                'fee_currency' => 'EUR',
                'starts_at' => $pastSeason->starts_at,
                'ends_at' => $pastSeason->ends_at,
            ]);
        });

        // Current season memberships — active, pending, free, cancelled (overdue)
        $memberships = collect();
        $athletes->each(function (User $athlete, $index) use ($bczTeam, $currentSeason, &$memberships) {
            // 0-4: ACTIVE (paid), 5: ACTIVE (free), 6: CANCELLED (overdue), 7-8: PENDING (waiting)
            if ($index <= 4) {
                $status = MembershipStatusEnum::ACTIVE;
                $isFree = false;
                $feeAmount = 50.00;
                $deadlineAt = null;
            } elseif ($index === 5) {
                $status = MembershipStatusEnum::ACTIVE;
                $isFree = true;
                $feeAmount = 0;
                $deadlineAt = null;
            } elseif ($index === 6) {
                $status = MembershipStatusEnum::CANCELLED;
                $isFree = false;
                $feeAmount = 50.00;
                $deadlineAt = now()->subDays(3);
            } else {
                $status = MembershipStatusEnum::PENDING;
                $isFree = false;
                // Mid-season join — prorated fee
                $feeAmount = $currentSeason->proratedFee();
                $deadlineAt = now()->addDays(rand(3, 12));
            }

            $startsAt = $index >= 7
                ? now()->subDays(rand(1, 5))
                : $currentSeason->starts_at;

            $membership = Membership::create([
                'team_id' => $bczTeam->id,
                'user_id' => $athlete->id,
                'team_season_id' => $currentSeason->id,
                'status' => $status,
                'fee_amount' => $feeAmount,
                'fee_currency' => 'EUR',
                'is_free' => $isFree,
                'payment_deadline_at' => $deadlineAt,
                'starts_at' => $startsAt,
                'ends_at' => $currentSeason->ends_at,
            ]);

            $memberships->push($membership);
        });

        // Members (line 191) — mid-season PENDING memberships (waiting for payment)
        $members->each(function (User $member) use ($bczTeam, $currentSeason, &$memberships) {
            $membership = Membership::create([
                'team_id' => $bczTeam->id,
                'user_id' => $member->id,
                'team_season_id' => $currentSeason->id,
                'status' => MembershipStatusEnum::PENDING,
                'fee_amount' => $currentSeason->proratedFee(),
                'fee_currency' => 'EUR',
                'is_free' => false,
                'payment_deadline_at' => now()->addDays(rand(5, 14)),
                'starts_at' => now()->subDays(rand(1, 7)),
                'ends_at' => $currentSeason->ends_at,
            ]);

            $memberships->push($membership);
        });

        // Judges — ACTIVE memberships (free, as judges)
        $judges->each(function (User $judge) use ($bczTeam, $currentSeason, &$memberships) {
            $membership = Membership::create([
                'team_id' => $bczTeam->id,
                'user_id' => $judge->id,
                'team_season_id' => $currentSeason->id,
                'status' => MembershipStatusEnum::ACTIVE,
                'fee_amount' => 0,
                'fee_currency' => 'EUR',
                'is_free' => true,
                'payment_deadline_at' => null,
                'starts_at' => $currentSeason->starts_at,
                'ends_at' => $currentSeason->ends_at,
            ]);

            $memberships->push($membership);
        });

        // Payments for memberships (completed)
        $memberships->where('status', MembershipStatusEnum::ACTIVE)->each(function (Membership $membership) use ($bczTeam) {
            $method = collect([PaymentMethodEnum::CASH, PaymentMethodEnum::BANK_TRANSFER, PaymentMethodEnum::CASH, PaymentMethodEnum::GOPAY])->random();

            Payment::create([
                'team_id' => $bczTeam->id,
                'user_id' => $membership->user_id,
                'payable_type' => 'membership',
                'payable_id' => $membership->id,
                'amount' => $membership->fee_amount,
                'currency' => $membership->fee_currency,
                'status' => PaymentStatusEnum::COMPLETED,
                'payment_method' => $method,
                'variable_symbol' => $method === PaymentMethodEnum::BANK_TRANSFER ? (string) rand(1000000000, 9999999999) : null,
                'gopay_payment_id' => $method === PaymentMethodEnum::GOPAY ? 'gp_demo_'.fake()->regexify('[a-zA-Z0-9]{16}') : null,
                'paid_at' => $membership->starts_at->addDays(rand(0, 7)),
            ]);
        });

        // Payments for training registrations — varied methods and statuses
        $trainingRegistrations = TrainingRegistration::whereNotNull('user_id')
            ->whereHas('training', fn ($q) => $q->where('pricing_type', TrainingPricingTypeEnum::PAID))
            ->with('training')
            ->get();

        $paymentMethods = [
            PaymentMethodEnum::GOPAY,
            PaymentMethodEnum::BANK_TRANSFER,
            PaymentMethodEnum::CASH,
            PaymentMethodEnum::CASH,
        ];

        // Most approved registrations get a completed payment
        $trainingRegistrations
            ->where('status', RegistrationStatusEnum::Approved)
            ->each(function (TrainingRegistration $registration, int $i) use ($bczTeam, $paymentMethods) {
                $method = $paymentMethods[$i % count($paymentMethods)];
                $amount = $registration->training->price_amount ?? 10.00;

                Payment::create([
                    'team_id' => $bczTeam->id,
                    'user_id' => $registration->user_id,
                    'payable_type' => 'training_registration',
                    'payable_id' => $registration->id,
                    'amount' => $amount,
                    'currency' => 'EUR',
                    'status' => PaymentStatusEnum::COMPLETED,
                    'payment_method' => $method,
                    'variable_symbol' => $method === PaymentMethodEnum::BANK_TRANSFER ? (string) rand(1000000000, 9999999999) : null,
                    'gopay_payment_id' => $method === PaymentMethodEnum::GOPAY ? 'gp_demo_'.fake()->regexify('[a-zA-Z0-9]{16}') : null,
                    'paid_at' => $registration->registered_at?->addHours(rand(1, 72)) ?? now()->subDays(rand(1, 30)),
                ]);
            });

        // A few pending registrations get a pending payment
        $trainingRegistrations
            ->where('status', RegistrationStatusEnum::Pending)
            ->take(3)
            ->each(function (TrainingRegistration $registration) use ($bczTeam) {
                $amount = $registration->training->price_amount ?? 10.00;

                Payment::create([
                    'team_id' => $bczTeam->id,
                    'user_id' => $registration->user_id,
                    'payable_type' => 'training_registration',
                    'payable_id' => $registration->id,
                    'amount' => $amount,
                    'currency' => 'EUR',
                    'status' => PaymentStatusEnum::PENDING,
                    'payment_method' => PaymentMethodEnum::BANK_TRANSFER,
                    'variable_symbol' => (string) rand(1000000000, 9999999999),
                ]);
            });

        // Payments for competition registrations
        $compRegistrations = EventRegistration::where('status', 'approved')
            ->whereHas('event', fn ($q) => $q->where('event_type', 'competition'))
            ->get();
        $compRegistrations->take(4)->each(function (EventRegistration $registration) use ($bczTeam) {
            Payment::create([
                'team_id' => $bczTeam->id,
                'user_id' => $registration->user_id,
                'payable_type' => 'competition_registration',
                'payable_id' => $registration->id,
                'amount' => 25.00,
                'currency' => 'EUR',
                'status' => PaymentStatusEnum::COMPLETED,
                'payment_method' => PaymentMethodEnum::BANK_TRANSFER,
                'variable_symbol' => (string) rand(1000000000, 9999999999),
                'paid_at' => now()->subDays(rand(10, 60)),
            ]);
        });

        // Pending payment
        Payment::create([
            'team_id' => $bczTeam->id,
            'user_id' => $athletes->last()->id,
            'payable_type' => 'membership',
            'payable_id' => $memberships->last()->id,
            'amount' => 20.00,
            'currency' => 'EUR',
            'status' => PaymentStatusEnum::PENDING,
            'payment_method' => PaymentMethodEnum::BANK_TRANSFER,
            'variable_symbol' => (string) rand(1000000000, 9999999999),
        ]);

        // Refunded payment for a cancelled training registration
        $cancelledRegistration = TrainingRegistration::where('status', RegistrationStatusEnum::Cancelled)->first();
        if ($cancelledRegistration) {
            Payment::create([
                'team_id' => $bczTeam->id,
                'user_id' => $cancelledRegistration->user_id,
                'payable_type' => 'training_registration',
                'payable_id' => $cancelledRegistration->id,
                'amount' => $cancelledRegistration->training?->price_amount ?? 15.00,
                'currency' => 'EUR',
                'status' => PaymentStatusEnum::REFUNDED,
                'payment_method' => PaymentMethodEnum::GOPAY,
                'gopay_payment_id' => 'pi_demo_refunded_'.fake()->regexify('[a-zA-Z0-9]{12}'),
                'paid_at' => now()->subDays(45),
                'refunded_at' => now()->subDays(30),
                'notes' => 'Zrušená registrácia na tréning.',
            ]);
        }

        // --- Team Subscriptions ---
        $freePlan = SubscriptionPlan::where('tier', 'free')->first();
        $proPlan = SubscriptionPlan::where('tier', 'pro')->first();

        if ($freePlan) {
            // BCZ team gets Pro plan
            if ($proPlan) {
                TeamSubscription::create([
                    'team_id' => $bczTeam->id,
                    'subscription_plan_id' => $proPlan->id,
                    'status' => SubscriptionStatusEnum::ACTIVE,
                    'billing_period' => BillingPeriodEnum::YEARLY,
                    'amount' => 789.00,
                    'currency' => 'EUR',
                    'starts_at' => now()->subMonths(3),
                    'ends_at' => now()->addMonths(9),
                ]);
            }

            // Second team gets Free plan
            TeamSubscription::create([
                'team_id' => $secondTeam->id,
                'subscription_plan_id' => $freePlan->id,
                'status' => SubscriptionStatusEnum::ACTIVE,
                'billing_period' => BillingPeriodEnum::MONTHLY,
                'amount' => 0,
                'currency' => 'EUR',
                'starts_at' => now()->subMonths(6),
            ]);
        }

        // --- Team Payouts ---
        TeamPayout::create([
            'team_id' => $bczTeam->id,
            'gross_amount' => 350.00,
            'fee_amount' => 17.50,
            'net_amount' => 332.50,
            'currency' => 'EUR',
            'status' => PayoutStatusEnum::COMPLETED,
            'bank_account_iban' => 'SK89 7500 0000 0000 1234 5678',
            'bank_account_name' => 'BCZ Club o.z.',
            'reference' => 'PAYOUT-2026-01',
            'period_from' => now()->subMonths(3)->startOfMonth(),
            'period_to' => now()->subMonths(3)->endOfMonth(),
            'paid_at' => now()->subMonths(2)->startOfMonth()->addDays(5),
        ]);

        TeamPayout::create([
            'team_id' => $bczTeam->id,
            'gross_amount' => 520.00,
            'fee_amount' => 26.00,
            'net_amount' => 494.00,
            'currency' => 'EUR',
            'status' => PayoutStatusEnum::COMPLETED,
            'bank_account_iban' => 'SK89 7500 0000 0000 1234 5678',
            'bank_account_name' => 'BCZ Club o.z.',
            'reference' => 'PAYOUT-2026-02',
            'period_from' => now()->subMonths(2)->startOfMonth(),
            'period_to' => now()->subMonths(2)->endOfMonth(),
            'paid_at' => now()->subMonth()->startOfMonth()->addDays(5),
        ]);

        TeamPayout::create([
            'team_id' => $bczTeam->id,
            'gross_amount' => 415.00,
            'fee_amount' => 20.75,
            'net_amount' => 394.25,
            'currency' => 'EUR',
            'status' => PayoutStatusEnum::PROCESSING,
            'bank_account_iban' => 'SK89 7500 0000 0000 1234 5678',
            'bank_account_name' => 'BCZ Club o.z.',
            'reference' => 'PAYOUT-2026-03',
            'period_from' => now()->subMonth()->startOfMonth(),
            'period_to' => now()->subMonth()->endOfMonth(),
        ]);

        TeamPayout::create([
            'team_id' => $bczTeam->id,
            'gross_amount' => 280.00,
            'fee_amount' => 14.00,
            'net_amount' => 266.00,
            'currency' => 'EUR',
            'status' => PayoutStatusEnum::PENDING,
            'bank_account_iban' => 'SK89 7500 0000 0000 1234 5678',
            'bank_account_name' => 'BCZ Club o.z.',
            'reference' => 'PAYOUT-2026-04',
            'period_from' => now()->startOfMonth(),
            'period_to' => now()->endOfMonth(),
        ]);

        // Seasons + memberships for second team
        $secondTeamPastSeason = TeamSeason::create([
            'team_id' => $secondTeam->id,
            'name' => 'Sezóna '.(now()->year - 1),
            'starts_at' => now()->subYear()->startOfYear()->month(1)->startOfMonth(),
            'ends_at' => now()->subYear()->startOfYear()->month(12)->endOfMonth(),
            'fee_amount' => 100.00,
            'fee_currency' => 'EUR',
            'payment_deadline_days' => 7,
        ]);

        $secondTeamSeason = TeamSeason::create([
            'team_id' => $secondTeam->id,
            'name' => 'Sezóna '.now()->year,
            'starts_at' => now()->startOfYear()->month(1)->startOfMonth(),
            'ends_at' => now()->startOfYear()->month(12)->endOfMonth(),
            'fee_amount' => 120.00,
            'fee_currency' => 'EUR',
            'max_capacity' => 10,
            'payment_deadline_days' => 7,
        ]);

        $secondTeamUsers = User::factory(5)->create();
        $secondTeamUsers->each(function (User $user, $index) use ($secondTeam, $secondTeamSeason, $secondTeamPastSeason) {
            $user->assignRole(RoleEnum::CUSTOMER);
            $user->teams()->attach($secondTeam, ['role' => RoleEnum::ATHLETE->value, 'is_active' => true, 'joined_at' => now()->subMonths(rand(1, 12))]);

            // Past season — expired memberships for first 3 users
            if ($index < 3) {
                Membership::create([
                    'team_id' => $secondTeam->id,
                    'user_id' => $user->id,
                    'team_season_id' => $secondTeamPastSeason->id,
                    'status' => MembershipStatusEnum::COMPLETED,
                    'fee_amount' => 100.00,
                    'fee_currency' => 'EUR',
                    'starts_at' => $secondTeamPastSeason->starts_at,
                    'ends_at' => $secondTeamPastSeason->ends_at,
                ]);
            }

            // Current season — varied statuses
            $isFree = $index === 0;
            $status = match (true) {
                $index <= 2 => MembershipStatusEnum::ACTIVE,
                $index === 3 => MembershipStatusEnum::PENDING,
                default => MembershipStatusEnum::CANCELLED,
            };

            Membership::create([
                'team_id' => $secondTeam->id,
                'user_id' => $user->id,
                'team_season_id' => $secondTeamSeason->id,
                'status' => $status,
                'fee_amount' => $isFree ? 0 : 120.00,
                'fee_currency' => 'EUR',
                'is_free' => $isFree,
                'payment_deadline_at' => $status === MembershipStatusEnum::PENDING ? now()->addDays(5) : null,
                'starts_at' => $secondTeamSeason->starts_at,
                'ends_at' => $secondTeamSeason->ends_at,
            ]);
        });

        // --- Banners ---

        // Rebranding topbar
        Banner::create([
            'name' => ['sk' => 'Rebranding', 'en' => 'Rebranding', 'cs' => 'Rebranding'],
            'type' => BannerTypeEnum::Topbar,
            'placement' => 'all',
            'content' => [
                'bg_color' => '#1A1A1A',
                'title' => [
                    'sk' => 'Nová značka, rovnaká vášeň: Street Workout Kysuce → BCZ Club',
                    'en' => 'New brand, same passion: Street Workout Kysuce → BCZ Club',
                    'cs' => 'Nová značka, stejná vášeň: Street Workout Kysuce → BCZ Club',
                ],
            ],
            'is_active' => true,
            'sort_order' => 10,
        ]);

        // 2% z dane floating window
        Banner::create([
            'name' => ['sk' => '2% z daní', 'en' => '2% tax donation', 'cs' => '2% z daní'],
            'type' => BannerTypeEnum::Floating,
            'placement' => 'all',
            'content' => [
                'icon' => 'heart',
                'bg_color' => '#FFFFFF',
                'title' => [
                    'sk' => 'Darujte nám 2% z dane',
                    'en' => 'Donate 2% of your taxes',
                    'cs' => 'Darujte nám 2% z daní',
                ],
                'description' => [
                    'sk' => 'Podporíte rozvoj parkouru na Slovensku a pomôžete nám vychovávať ďalšiu generáciu športovcov.',
                    'en' => 'Support the development of parkour in Slovakia and help us raise the next generation of athletes.',
                    'cs' => 'Podpoříte rozvoj parkouru na Slovensku a pomůžete nám vychovávat další generaci sportovců.',
                ],
                'stat1_value' => '500+',
                'stat1_label' => [
                    'sk' => 'detí ročne',
                    'en' => 'kids per year',
                    'cs' => 'dětí ročně',
                ],
                'stat2_value' => '10+',
                'stat2_label' => [
                    'sk' => 'rokov',
                    'en' => 'years',
                    'cs' => 'let',
                ],
                'primary_button_text' => [
                    'sk' => 'ZÍSKAŤ TLAČIVO',
                    'en' => 'GET THE FORM',
                    'cs' => 'ZÍSKAT FORMULÁŘ',
                ],
                'primary_button_link_type' => 'custom',
                'primary_button_link_url' => [
                    'sk' => '/2-percenta',
                    'en' => '/en/2-percenta',
                    'cs' => '/cs/2-percenta',
                ],
                'note' => [
                    'sk' => 'IČO: 42 195 250 • Právna forma: občianske združenie',
                    'en' => 'ID: 42 195 250 • Legal form: civic association',
                    'cs' => 'IČO: 42 195 250 • Právní forma: občanské sdružení',
                ],
            ],
            'is_active' => true,
            'active_from' => now()->startOfYear()->toDateTimeString(),
            'active_to' => now()->month(4)->endOfMonth()->toDateTimeString(),
            'sort_order' => 10,
        ]);

        $this->chainCompetitionRounds();
    }

    /**
     * Link rounds into a chain via next_round_id and populate competitor_count.
     *
     * Within each (competition_detail_id, athlete_category_id) group, sort by
     * sort_order + round_number and point each round at its successor. Fills
     * competitor_count from actual battle pivot rows (battle rounds) or
     * approved registration counts (qualification rounds).
     */
    private function chainCompetitionRounds(): void
    {
        $rounds = CompetitionRound::query()
            ->orderBy('competition_detail_id')
            ->orderBy('athlete_category_id')
            ->orderBy('sort_order')
            ->orderBy('round_number')
            ->get()
            ->groupBy(fn ($r) => $r->competition_detail_id.'|'.($r->athlete_category_id ?? 'null'));

        foreach ($rounds as $chain) {
            $ordered = $chain->values();
            for ($i = 0; $i < $ordered->count() - 1; $i++) {
                $ordered[$i]->update(['next_round_id' => $ordered[$i + 1]->id]);
            }
        }

        foreach (CompetitionRound::all() as $round) {
            if ($round->isBattle()) {
                $count = BattleCompetitor::whereIn(
                    'battle_id',
                    $round->battles()->pluck('id'),
                )->count();
                if ($count > 0) {
                    $round->update(['competitor_count' => $count]);
                }

                continue;
            }

            $detail = $round->competitionDetail;
            if (! $detail || $round->athlete_category_id === null) {
                continue;
            }

            $count = EventRegistration::query()
                ->where('event_id', $detail->event_id)
                ->where('athlete_category_id', $round->athlete_category_id)
                ->where('status', RegistrationStatusEnum::Approved)
                ->count();

            if ($count > 0) {
                $round->update(['competitor_count' => $count]);
            }
        }
    }
}
