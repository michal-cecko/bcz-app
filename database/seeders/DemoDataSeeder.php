<?php

namespace Database\Seeders;

use App\Enums\CoachRoleEnum;
use App\Enums\ComplexityLevelEnum;
use App\Enums\GenderEnum;
use App\Enums\InquiryReasonEnum;
use App\Enums\InquiryStatusEnum;
use App\Enums\MembershipPeriodEnum;
use App\Enums\MembershipStatusEnum;
use App\Enums\PaymentMethodEnum;
use App\Enums\PaymentStatusEnum;
use App\Enums\PayoutStatusEnum;
use App\Enums\RoleEnum;
use App\Enums\RoundAdvancementTypeEnum;
use App\Enums\SponsorTagEnum;
use App\Enums\SubscriptionStatusEnum;
use App\Enums\TimetableEntryStatusEnum;
use App\Enums\TrainingPricingTypeEnum;
use App\Models\AthleteCategory;
use App\Models\AthleteExercise;
use App\Models\AthleteGoal;
use App\Models\AthleteProfile;
use App\Models\Battle;
use App\Models\Certification;
use App\Models\CoachProfile;
use App\Models\Competition;
use App\Models\CompetitionRegistration;
use App\Models\CompetitionReport;
use App\Models\CompetitionResult;
use App\Models\CompetitionRound;
use App\Models\Discipline;
use App\Models\Event;
use App\Models\EventCategory;
use App\Models\Exercise;
use App\Models\ExerciseCategory;
use App\Models\Faq;
use App\Models\FaqCategory;
use App\Models\Inquiry;
use App\Models\MediaLibraryItem;
use App\Models\Membership;
use App\Models\Payment;
use App\Models\RegistrationFee;
use App\Models\RoundPart;
use App\Models\Sponsor;
use App\Models\SportCategory;
use App\Models\SubscriptionPlan;
use App\Models\Team;
use App\Models\TeamPayout;
use App\Models\TeamSubscription;
use App\Models\TimetableEntry;
use App\Models\Training;
use App\Models\TrainingRegistration;
use App\Models\User;
use App\Services\MediaLibraryFolderService;
use Illuminate\Database\Seeder;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        $bczTeam = Team::query()->where('slug', 'bcz-club')->firstOrFail();
        $secondTeam = Team::factory()->create([
            'name' => ['sk' => 'Gravity Crew', 'en' => 'Gravity Crew', 'cz' => 'Gravity Crew'],
        ]);

        $folderService = app(MediaLibraryFolderService::class);

        $sportCategories = SportCategory::all();
        $sportCategories->each(fn (SportCategory $cat) => $cat->update(['team_id' => $bczTeam->id]));
        $parkour = $sportCategories->firstWhere('slug', 'parkour-freerunning');
        $streetWorkout = $sportCategories->firstWhere('slug', 'street-workout');

        // --- Users with roles ---
        $coachData = [
            [
                'name' => 'Michal Čečko',
                'email' => 'michal@bczclub.com',
                'biography' => [
                    'sk' => '8 rokov aktívneho tréningu a 5 rokov skúseností s vedením skupín. Michal sa špecializuje na výuku techniky a bezpečný progres. Jeho tréningy sú známe skvelou atmosférou a individuálnym prístupom ku každému účastníkovi.',
                    'en' => '8 years of active training and 5 years of experience leading groups. Michal specializes in technique instruction and safe progression. His trainings are known for their great atmosphere and individual approach to each participant.',
                ],
            ],
            [
                'name' => 'Dominik Klimek',
                'email' => 'dominik@bczclub.com',
                'biography' => [
                    'sk' => 'Spoluzakladateľ BCZ Club a profesionálny parkour atléta s 10 rokmi skúseností. Dominik vedie pokročilé tréningy a pripravuje atlétov na súťaže. Je držiteľom certifikátu A.D.A.P.T. a pravidelne sa zúčastňuje medzinárodných workshopov.',
                    'en' => 'Co-founder of BCZ Club and professional parkour athlete with 10 years of experience. Dominik leads advanced trainings and prepares athletes for competitions. He holds the A.D.A.P.T. certificate and regularly participates in international workshops.',
                ],
            ],
            [
                'name' => 'Tomáš Bartek',
                'email' => 'tomas@bczclub.com',
                'biography' => [
                    'sk' => 'Certifikovaný tréner kalisteniky a street workoutu. Tomáš má za sebou 6 rokov súťažného street workoutu a viacero umiestnení na slovenských a českých súťažiach. Zameriava sa na silový tréning s vlastnou váhou a progresiu k náročným prvkom.',
                    'en' => 'Certified calisthenics and street workout coach. Tomáš has 6 years of competitive street workout experience and multiple placements at Slovak and Czech competitions. He focuses on bodyweight strength training and progression to advanced elements.',
                ],
            ],
        ];

        $coaches = collect($coachData)->map(function ($data, $index) use ($bczTeam, $folderService) {
            $user = User::factory()->create([
                'name' => $data['name'],
                'email' => $data['email'],
            ]);
            $user->assignRole(RoleEnum::COACH);
            $user->teams()->attach($bczTeam, ['is_active' => true, 'joined_at' => now()->subMonths(rand(6, 36))]);

            $coachFolder = $folderService->ensureUserFolder($user, $bczTeam);
            $photoItem = MediaLibraryItem::create(['folder_id' => $coachFolder->id]);
            $photoItem->addMediaFromUrl("https://picsum.photos/seed/coach-{$index}/400/500")
                ->usingFileName("coach-{$index}.jpg")
                ->toMediaCollection('library');

            CoachProfile::factory()->create([
                'user_id' => $user->id,
                'biography' => $data['biography'],
                'biography_image' => $photoItem->id,
            ]);

            return $user;
        });

        $athletes = User::factory(8)->create()->each(function (User $user, int $index) use ($bczTeam) {
            $user->assignRole(RoleEnum::ATHLETE);
            $user->teams()->attach($bczTeam, ['is_active' => true, 'joined_at' => now()->subMonths(rand(1, 24))]);
            AthleteProfile::factory()->create(['user_id' => $user->id]);

            // Give first 5 athletes a public profile (approved)
            if ($index < 5) {
                $user->update([
                    'has_public_profile' => true,
                    'public_profile_approved_at' => now()->subDays(rand(1, 60)),
                ]);
            }
        });

        $judgeData = [
            [
                'name' => 'Peter Novák',
                'country_code' => 'SK',
                'certifications' => [
                    ['name' => ['sk' => 'WSWCF Level A', 'en' => 'WSWCF Level A'], 'description' => ['sk' => 'Medzinárodná rozhodcovská licencia World Street Workout & Calisthenics Federation', 'en' => 'International judge license from World Street Workout & Calisthenics Federation'], 'year_of_issue' => 2021],
                    ['name' => ['sk' => 'Hlavný porotca SR', 'en' => 'Head Judge SK'], 'description' => ['sk' => 'Oprávnenie hlavného porotcu pre súťaže na Slovensku', 'en' => 'Head judge authorization for competitions in Slovakia'], 'year_of_issue' => 2023],
                ],
            ],
            [
                'name' => 'Tomáš Horváth',
                'country_code' => 'SK',
                'certifications' => [
                    ['name' => ['sk' => 'FIG Parkour Judge', 'en' => 'FIG Parkour Judge'], 'description' => ['sk' => 'Medzinárodná rozhodcovská licencia Fédération Internationale de Gymnastique', 'en' => 'International judge license from Fédération Internationale de Gymnastique'], 'year_of_issue' => 2022],
                ],
            ],
            [
                'name' => 'Marek Kováč',
                'country_code' => 'CZ',
                'certifications' => [
                    ['name' => ['sk' => 'WSWCF Level B', 'en' => 'WSWCF Level B'], 'description' => ['sk' => 'Rozhodcovská licencia World Street Workout & Calisthenics Federation', 'en' => 'Judge license from World Street Workout & Calisthenics Federation'], 'year_of_issue' => 2023],
                    ['name' => ['sk' => 'Porotca Freestyle', 'en' => 'Freestyle Judge'], 'description' => ['sk' => 'Špecializácia na hodnotenie freestyle disciplín', 'en' => 'Specialization in freestyle discipline judging'], 'year_of_issue' => 2024],
                ],
            ],
            [
                'name' => 'Jakub Vlček',
                'country_code' => 'SK',
                'certifications' => [
                    ['name' => ['sk' => 'BCZ Certified Judge', 'en' => 'BCZ Certified Judge'], 'description' => ['sk' => 'Interná rozhodcovská certifikácia BCZ Club', 'en' => 'Internal BCZ Club judge certification'], 'year_of_issue' => 2025],
                ],
            ],
        ];

        $judges = collect($judgeData)->map(function ($data) use ($bczTeam) {
            $user = User::factory()->create([
                'name' => $data['name'],
                'country_code' => $data['country_code'],
            ]);
            $user->assignRole(RoleEnum::JUDGE);
            $user->teams()->attach($bczTeam, ['is_active' => true, 'joined_at' => now()->subMonths(rand(3, 12))]);

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
            $user->assignRole(RoleEnum::ATHLETE);
            $user->teams()->attach($bczTeam, ['is_active' => true, 'joined_at' => now()->subMonths(rand(1, 6))]);
        });

        // --- Media Library Folder Structure ---
        $teamFolder = $folderService->ensureTeamFolder($bczTeam);

        // Athlete folders inside "Atléti" subfolder
        $athletes->each(function (User $user, int $index) use ($folderService, $bczTeam) {
            $athleteFolder = $folderService->ensureAthleteFolder($user, $bczTeam);

            // Upload placeholder profile picture
            $item = MediaLibraryItem::create(['folder_id' => $athleteFolder->id]);
            $item->addMediaFromUrl("https://picsum.photos/seed/athlete-{$index}/400/500")
                ->usingFileName("profile-{$index}.jpg")
                ->toMediaCollection('library');
        });

        // Upload team logo placeholder
        $logoItem = MediaLibraryItem::create(['folder_id' => $teamFolder->id]);
        $logoItem->addMediaFromUrl('https://picsum.photos/seed/bcz-logo/200/200')
            ->usingFileName('bcz-club-logo.png')
            ->toMediaCollection('library');
        $bczTeam->update(['logo' => $logoItem->id]);

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
            ['label' => ['sk' => 'Meno', 'en' => 'First name', 'cs' => 'Jméno'], 'name' => 'meno', 'type' => 'text_input', 'width' => 'half', 'required' => true, 'has_condition' => false],
            ['label' => ['sk' => 'Priezvisko', 'en' => 'Last name', 'cs' => 'Příjmení'], 'name' => 'priezvisko', 'type' => 'text_input', 'width' => 'half', 'required' => true, 'has_condition' => false],
            ['label' => ['sk' => 'Email', 'en' => 'Email', 'cs' => 'Email'], 'name' => 'email', 'type' => 'email', 'width' => 'full', 'required' => true, 'placeholder' => ['sk' => 'tvoj@email.sk', 'en' => 'your@email.com', 'cs' => 'tvuj@email.cz'], 'has_condition' => false],
        ];

        $parentFields = [
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
            // 0: Parkour Teens (kids → parent fields + age + phone + note)
            array_merge($parentFields, [$extraAge, $extraPhone, $extraNote]),
            // 1: Street Workout Advanced (standard + experience + note)
            array_merge($mandatoryFields, [$extraPhone, $extraExperience, $extraNote]),
            // 2: Parkour pre pokročilých (standard + experience + year + insurance)
            array_merge($mandatoryFields, [$extraPhone, $extraExperience, $extraYear, $extraInsurance, $extraInsuranceDetail]),
            // 3: Kalistenické základy (kids → parent fields + age)
            array_merge($parentFields, [$extraAge, $extraPhone]),
            // 4: Street Workout pre deti (kids → parent fields + age + tshirt + note)
            array_merge($parentFields, [$extraAge, $extraTshirt, $extraPhone, $extraNote]),
            // 5: Parkour & Freerunning Mix (standard + experience)
            array_merge($mandatoryFields, [$extraPhone, $extraExperience]),
            // 6: Open Gym (standard only, minimal)
            array_merge($mandatoryFields, [$extraPhone]),
            // 7: Tricking Workshop (standard + experience + year + tshirt + note)
            array_merge($mandatoryFields, [$extraPhone, $extraYear, $extraExperience, $extraTshirt, $extraNote]),
            // 8: (if more trainings exist, fallback)
            array_merge($mandatoryFields, [$extraPhone, $extraNote]),
        ];

        // --- Trainings ---
        $trainings = collect([
            [
                'title' => ['sk' => 'Parkour Teens', 'en' => 'Parkour Teens'],
                'description' => [
                    'sk' => "Parkour Teens je skupinový tréning určený pre mladých vo veku 13-17 rokov. Naučíš sa základy parkouru a freerunningU - od bezpečných pádov, cez preskoky a výstupy, až po dynamické pohyby a salto.\n\nTréningy sú zamerané na postupný progres, správnu techniku a hlavne zábavu v skvelej komunite.",
                    'en' => "Parkour Teens is a group training designed for youth aged 13-17. You will learn the basics of parkour and freerunning - from safe falls, to jumps and climbs, to dynamic movements and flips.\n\nTrainings focus on gradual progression, proper technique, and most importantly having fun in a great community.",
                ],
                'sport_category_id' => $parkour->id,
                'pricing_type' => TrainingPricingTypeEnum::PAID,
                'price_amount' => 8.00,
                'age_group' => '13-17',
                'max_capacity' => 12,
                'schedule_days' => ['monday', 'wednesday'],
                'start_time' => '17:00',
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
                'pricing_type' => TrainingPricingTypeEnum::PAID,
                'price_amount' => 12.00,
                'age_group' => '16+',
                'max_capacity' => 15,
                'schedule_days' => ['tuesday', 'thursday'],
                'start_time' => '18:00',
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
                'age_group' => '14-25',
                'max_capacity' => 20,
                'schedule_days' => ['wednesday', 'friday'],
                'start_time' => '17:30',
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
                'pricing_type' => TrainingPricingTypeEnum::PAID,
                'price_amount' => 15.00,
                'age_group' => '16+',
                'max_capacity' => 10,
                'schedule_days' => ['saturday'],
                'start_time' => '10:00',
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
                'age_group' => '10-16',
                'max_capacity' => 20,
                'schedule_days' => ['monday', 'thursday'],
                'start_time' => '16:00',
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
                'pricing_type' => TrainingPricingTypeEnum::PAID,
                'price_amount' => 6.00,
                'age_group' => '8-14',
                'max_capacity' => 18,
                'schedule_days' => ['tuesday', 'friday'],
                'start_time' => '15:30',
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
                'age_group' => '14-25',
                'max_capacity' => 16,
                'schedule_days' => ['monday', 'wednesday', 'friday'],
                'start_time' => '19:00',
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
                'age_group' => '16+',
                'max_capacity' => 30,
                'schedule_days' => ['saturday', 'sunday'],
                'start_time' => '09:00',
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
                'age_group' => '14-25',
                'max_capacity' => 10,
                'schedule_days' => ['sunday'],
                'start_time' => '14:00',
                'duration_minutes' => 120,
                'place_name' => ['sk' => 'BCZ Gym Bratislava', 'en' => 'BCZ Gym Bratislava'],
                'place_address' => 'Stará Vajnorská 37, 831 04 Bratislava',
                'gathering_place' => ['sk' => 'Zraz pri recepcii BCZ Gym. Prineste si vlastné chrániče (voliteľné).', 'en' => 'Meeting at BCZ Gym reception. Bring your own protection gear (optional).'],
                'latitude' => 48.1698,
                'longitude' => 17.1436,
            ],
        ])->map(function ($data, $index) use ($bczTeam, $registrationSchemas) {
            return Training::factory()->create(array_merge($data, [
                'team_id' => $bczTeam->id,
                'sort_order' => $index,
                'registration_form_schema' => $registrationSchemas[$index] ?? $registrationSchemas[count($registrationSchemas) - 1],
            ]));
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

        // Training registrations — varied capacity to show green/orange/red states
        $fillRatios = [0.0, 0.2, 0.5, 0.7, 0.75, 0.85, 0.92, 0.95, 1.0];
        $trainings->each(function (Training $training, int $index) use ($fillRatios) {
            $ratio = $fillRatios[$index % count($fillRatios)];
            $registrationCount = (int) round($training->max_capacity * $ratio);

            for ($i = 0; $i < $registrationCount; $i++) {
                TrainingRegistration::factory()->forTraining($training)->create([
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
        ])->map(function ($data) {
            return EventCategory::factory()->create(array_merge($data, ['is_active' => true]));
        });

        // --- Events ---
        $eventCategories->each(function (EventCategory $category) use ($bczTeam) {
            Event::factory(rand(2, 4))->create([
                'event_category_id' => $category->id,
                'team_id' => $bczTeam->id,
            ]);
        });

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

        // --- Competitions ---
        $pastCompetition = Competition::factory()->create([
            'name' => ['sk' => 'BCZ Championship 2025', 'en' => 'BCZ Championship 2025'],
            'description' => ['sk' => 'Hlavná súťaž sezóny 2025.'],
            'organizer_team_id' => $bczTeam->id,
            'date_start' => now()->subMonths(3),
            'date_end' => now()->subMonths(3)->addDay(),
            'place_name' => 'Športová hala Bratislava',
            'place_address' => 'Junácka 6, 831 04 Bratislava',
            'country' => 'Slovensko',
            'city' => 'Bratislava',
            'is_published' => true,
            'published_at' => now()->subMonths(5),
            'registration_opens_at' => now()->subMonths(5),
            'registration_closes_at' => now()->subMonths(3)->subWeek(),
            'is_public_registration' => true,
        ]);

        $upcomingCompetition = Competition::factory()->create([
            'name' => ['sk' => 'BCZ Spring Cup 2026', 'en' => 'BCZ Spring Cup 2026'],
            'description' => ['sk' => 'Jarná súťaž v parkour a street workout disciplínach.'],
            'organizer_team_id' => $bczTeam->id,
            'date_start' => now()->addMonths(2),
            'date_end' => now()->addMonths(2)->addDay(),
            'place_name' => 'Outdoor Park Košice',
            'place_address' => 'Hlavná 1, 040 01 Košice',
            'country' => 'Slovensko',
            'city' => 'Košice',
            'is_published' => true,
            'published_at' => now()->subWeek(),
            'registration_opens_at' => now()->subWeek(),
            'registration_closes_at' => now()->addMonth(),
            'is_public_registration' => true,
            'show_countdown' => true,
        ]);

        $competitions = collect([$pastCompetition, $upcomingCompetition]);

        // Attach disciplines, categories, judges
        $competitions->each(function (Competition $competition) use ($disciplines, $allCategories, $judges) {
            $competition->disciplines()->attach($disciplines->pluck('id'));
            $competition->athleteCategories()->attach($allCategories->pluck('id'));

            $judges->each(function (User $judge) use ($competition, $disciplines) {
                $competition->judges()->attach($judge->id, [
                    'discipline_id' => $disciplines->random()->id,
                ]);
            });
        });

        // Registration fees
        $competitions->each(function (Competition $competition) use ($allCategories) {
            RegistrationFee::factory()->create([
                'competition_id' => $competition->id,
                'amount' => 25.00,
                'currency' => 'EUR',
                'description' => 'Standard registration fee',
            ]);
            RegistrationFee::factory()->create([
                'competition_id' => $competition->id,
                'athlete_category_id' => $allCategories->last()->id,
                'amount' => 15.00,
                'currency' => 'EUR',
                'description' => 'Junior discount fee',
            ]);
        });

        // Timetable entries for past competition
        $timetableEntries = [
            ['title' => ['sk' => 'Registrácia', 'en' => 'Registration'], 'scheduled_time' => $pastCompetition->date_start->setTime(8, 0), 'status' => TimetableEntryStatusEnum::FINISHED],
            ['title' => ['sk' => 'Otvorenie', 'en' => 'Opening Ceremony'], 'scheduled_time' => $pastCompetition->date_start->setTime(9, 0), 'status' => TimetableEntryStatusEnum::FINISHED],
            ['title' => ['sk' => 'Speed Run - Kvalifikácia', 'en' => 'Speed Run - Qualification'], 'scheduled_time' => $pastCompetition->date_start->setTime(9, 30), 'status' => TimetableEntryStatusEnum::FINISHED],
            ['title' => ['sk' => 'Freestyle - Finále', 'en' => 'Freestyle - Finals'], 'scheduled_time' => $pastCompetition->date_start->setTime(14, 0), 'status' => TimetableEntryStatusEnum::FINISHED],
            ['title' => ['sk' => 'Vyhlásenie výsledkov', 'en' => 'Award Ceremony'], 'scheduled_time' => $pastCompetition->date_start->setTime(17, 0), 'status' => TimetableEntryStatusEnum::FINISHED],
        ];

        foreach ($timetableEntries as $i => $entry) {
            TimetableEntry::factory()->create(array_merge($entry, [
                'competition_id' => $pastCompetition->id,
                'sort_order' => $i,
            ]));
        }

        // Upcoming competition timetable
        $upcomingEntries = [
            ['title' => ['sk' => 'Registrácia', 'en' => 'Registration'], 'scheduled_time' => $upcomingCompetition->date_start->setTime(8, 0), 'status' => TimetableEntryStatusEnum::PENDING],
            ['title' => ['sk' => 'Otvorenie', 'en' => 'Opening Ceremony'], 'scheduled_time' => $upcomingCompetition->date_start->setTime(9, 0), 'status' => TimetableEntryStatusEnum::PENDING],
            ['title' => ['sk' => 'Speed Run', 'en' => 'Speed Run'], 'scheduled_time' => $upcomingCompetition->date_start->setTime(10, 0), 'status' => TimetableEntryStatusEnum::PENDING],
            ['title' => ['sk' => 'Battle', 'en' => 'Battle'], 'scheduled_time' => $upcomingCompetition->date_start->setTime(14, 0), 'status' => TimetableEntryStatusEnum::PENDING],
        ];

        foreach ($upcomingEntries as $i => $entry) {
            TimetableEntry::factory()->create(array_merge($entry, [
                'competition_id' => $upcomingCompetition->id,
                'sort_order' => $i,
            ]));
        }

        // Competition registrations
        $athletes->each(function (User $athlete, $index) use ($pastCompetition, $upcomingCompetition, $allCategories) {
            $category = $allCategories[$index % 3];
            CompetitionRegistration::factory()->create([
                'competition_id' => $pastCompetition->id,
                'user_id' => $athlete->id,
                'athlete_category_id' => $category->id,
                'status' => 'confirmed',
                'weight_in' => rand(55, 90) + (rand(0, 9) / 10),
            ]);

            if ($index < 5) {
                CompetitionRegistration::factory()->create([
                    'competition_id' => $upcomingCompetition->id,
                    'user_id' => $athlete->id,
                    'athlete_category_id' => $category->id,
                    'status' => 'pending',
                ]);
            }
        });

        // Competition rounds & results for past competition
        $qualRound = CompetitionRound::factory()->create([
            'competition_id' => $pastCompetition->id,
            'athlete_category_id' => $menCategory->id,
            'round_number' => 1,
            'name' => 'Qualification',
            'advancement_type' => RoundAdvancementTypeEnum::TOP_BY_POINTS,
            'advance_count' => 4,
            'sort_order' => 1,
        ]);

        $finalRound = CompetitionRound::factory()->create([
            'competition_id' => $pastCompetition->id,
            'athlete_category_id' => $menCategory->id,
            'round_number' => 2,
            'name' => 'Final',
            'advancement_type' => RoundAdvancementTypeEnum::BATTLE_WINNER,
            'battle_size' => 1,
            'sort_order' => 2,
        ]);

        // Round parts and results
        $part1 = RoundPart::factory()->create([
            'competition_round_id' => $qualRound->id,
            'name' => ['sk' => 'Statics', 'en' => 'Statics'],
            'duration_seconds' => 30,
            'sort_order' => 1,
        ]);

        $part2 = RoundPart::factory()->create([
            'competition_round_id' => $qualRound->id,
            'name' => ['sk' => 'Dynamics', 'en' => 'Dynamics'],
            'duration_seconds' => 45,
            'sort_order' => 2,
        ]);

        $athletes->take(6)->each(function (User $athlete, $index) use ($part1, $part2) {
            CompetitionResult::factory()->create([
                'round_part_id' => $part1->id,
                'user_id' => $athlete->id,
                'score' => round(rand(60, 95) + (rand(0, 9) / 10), 1),
                'place' => $index + 1,
            ]);
            CompetitionResult::factory()->create([
                'round_part_id' => $part2->id,
                'user_id' => $athlete->id,
                'score' => round(rand(55, 98) + (rand(0, 9) / 10), 1),
                'place' => $index + 1,
            ]);
        });

        // Battles in final round
        $topAthletes = $athletes->take(4);
        Battle::factory()->create([
            'competition_round_id' => $finalRound->id,
            'athlete_category_id' => $menCategory->id,
            'bracket_position' => 1,
            'competitor_a_id' => [$topAthletes[0]->id],
            'competitor_b_id' => [$topAthletes[3]->id],
            'winner_id' => [$topAthletes[0]->id],
        ]);
        Battle::factory()->create([
            'competition_round_id' => $finalRound->id,
            'athlete_category_id' => $menCategory->id,
            'bracket_position' => 2,
            'competitor_a_id' => [$topAthletes[1]->id],
            'competitor_b_id' => [$topAthletes[2]->id],
            'winner_id' => [$topAthletes[1]->id],
        ]);

        // Competition report
        CompetitionReport::factory()->create([
            'competition_id' => $pastCompetition->id,
            'user_id' => $athletes->first()->id,
            'title' => ['sk' => 'Moje BCZ Championship 2025', 'en' => 'My BCZ Championship 2025'],
            'is_published' => true,
            'published_at' => now()->subMonths(2),
        ]);

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
        $webContentFolder = $folderService->ensureWebContentFolder();

        $sponsorData = [
            ['name' => 'Red Bull', 'tag' => SponsorTagEnum::MAIN_SPONSOR, 'link' => 'https://www.redbull.com/sk-sk', 'is_visible' => true, 'sort_order' => 1],
            ['name' => 'Nike', 'tag' => SponsorTagEnum::MAIN_SPONSOR, 'link' => 'https://www.nike.com', 'is_visible' => true, 'sort_order' => 2],
            ['name' => 'Denník N', 'tag' => SponsorTagEnum::MEDIAL_SPONSOR, 'link' => 'https://dennikn.sk', 'is_visible' => true, 'sort_order' => 3],
            ['name' => 'Město Bratislava', 'tag' => SponsorTagEnum::PARTNER, 'link' => 'https://bratislava.sk', 'is_visible' => true, 'sort_order' => 4],
            ['name' => 'Decathlon', 'tag' => SponsorTagEnum::SUPPORTER, 'link' => 'https://www.decathlon.sk', 'is_visible' => true, 'sort_order' => 5],
            ['name' => 'GymBeam', 'tag' => SponsorTagEnum::SUPPORTER, 'link' => 'https://www.gymbeam.sk', 'is_visible' => false, 'sort_order' => 6],
        ];

        foreach ($sponsorData as $index => $data) {
            $logoItem = MediaLibraryItem::create(['folder_id' => $webContentFolder->id]);
            $logoItem->addMediaFromUrl("https://picsum.photos/seed/sponsor-{$index}/200/100")
                ->usingFileName('sponsor-'.\Illuminate\Support\Str::slug($data['name']).'.png')
                ->toMediaCollection('library');

            Sponsor::factory()->create(array_merge($data, [
                'logo' => $logoItem->id,
            ]));
        }

        // --- Phase 5-6: Memberships, Payments, Subscriptions, Payouts ---

        // Enable membership on BCZ team
        $bczTeam->update([
            'membership_enabled' => true,
            'membership_fee_amount' => 20.00,
            'membership_fee_currency' => 'EUR',
            'membership_period' => MembershipPeriodEnum::YEARLY,
            'membership_description' => 'Ročné členstvo v BCZ Club zahŕňa prístup k tréningom a zľavy na súťaže.',
            'bank_account_iban' => 'SK89 7500 0000 0000 1234 5678',
            'bank_account_name' => 'BCZ Club o.z.',
        ]);

        // Memberships for athletes
        $memberships = collect();
        $athletes->each(function (User $athlete, $index) use ($bczTeam, &$memberships) {
            $status = $index < 5 ? MembershipStatusEnum::ACTIVE : ($index < 7 ? MembershipStatusEnum::EXPIRED : MembershipStatusEnum::PENDING);
            $startsAt = $status === MembershipStatusEnum::EXPIRED ? now()->subYear()->subMonth() : now()->subMonths(rand(1, 6));
            $endsAt = $status === MembershipStatusEnum::EXPIRED ? now()->subMonth() : now()->addMonths(rand(3, 12));

            $membership = Membership::create([
                'team_id' => $bczTeam->id,
                'user_id' => $athlete->id,
                'status' => $status,
                'period' => MembershipPeriodEnum::YEARLY,
                'fee_amount' => 20.00,
                'fee_currency' => 'EUR',
                'starts_at' => $startsAt,
                'ends_at' => $endsAt,
            ]);

            $memberships->push($membership);
        });

        // Payments for memberships (completed)
        $memberships->where('status', MembershipStatusEnum::ACTIVE)->each(function (Membership $membership) use ($bczTeam) {
            $method = collect([PaymentMethodEnum::MANUAL, PaymentMethodEnum::BANK_TRANSFER, PaymentMethodEnum::CASH, PaymentMethodEnum::STRIPE])->random();

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
                'stripe_payment_id' => $method === PaymentMethodEnum::STRIPE ? 'pi_demo_'.fake()->regexify('[a-zA-Z0-9]{16}') : null,
                'paid_at' => $membership->starts_at->addDays(rand(0, 7)),
            ]);
        });

        // Payments for training registrations
        $trainingRegistrations = TrainingRegistration::where('user_id', '!=', null)->get();
        $trainingRegistrations->take(3)->each(function (TrainingRegistration $registration) use ($bczTeam) {
            Payment::create([
                'team_id' => $bczTeam->id,
                'user_id' => $registration->user_id,
                'payable_type' => 'training_registration',
                'payable_id' => $registration->id,
                'amount' => 15.00,
                'currency' => 'EUR',
                'status' => PaymentStatusEnum::COMPLETED,
                'payment_method' => PaymentMethodEnum::STRIPE,
                'stripe_payment_id' => 'pi_demo_'.fake()->regexify('[a-zA-Z0-9]{16}'),
                'paid_at' => now()->subDays(rand(1, 30)),
            ]);
        });

        // Payments for competition registrations
        $compRegistrations = CompetitionRegistration::where('status', 'confirmed')->get();
        $compRegistrations->take(4)->each(function (CompetitionRegistration $registration) use ($bczTeam) {
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

        // Refunded payment
        Payment::create([
            'team_id' => $bczTeam->id,
            'user_id' => $members->first()->id,
            'payable_type' => 'training_registration',
            'payable_id' => $trainingRegistrations->first()->id,
            'amount' => 15.00,
            'currency' => 'EUR',
            'status' => PaymentStatusEnum::REFUNDED,
            'payment_method' => PaymentMethodEnum::STRIPE,
            'stripe_payment_id' => 'pi_demo_refunded_'.fake()->regexify('[a-zA-Z0-9]{12}'),
            'paid_at' => now()->subDays(45),
            'refunded_at' => now()->subDays(30),
            'notes' => 'Zrušená registrácia na tréning.',
        ]);

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
                    'billing_period' => MembershipPeriodEnum::YEARLY,
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
                'billing_period' => MembershipPeriodEnum::MONTHLY,
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

        // Memberships for second team
        User::factory(3)->create()->each(function (User $user) use ($secondTeam) {
            $user->assignRole(RoleEnum::ATHLETE);
            $user->teams()->attach($secondTeam, ['is_active' => true, 'joined_at' => now()->subMonths(rand(1, 6))]);

            Membership::create([
                'team_id' => $secondTeam->id,
                'user_id' => $user->id,
                'status' => MembershipStatusEnum::ACTIVE,
                'period' => MembershipPeriodEnum::MONTHLY,
                'fee_amount' => 10.00,
                'fee_currency' => 'EUR',
                'starts_at' => now()->subMonth(),
                'ends_at' => now()->addMonths(11),
            ]);
        });
    }
}
