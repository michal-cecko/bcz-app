<?php

namespace Database\Seeders;

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

        $coaches = collect($coachData)->map(function ($data, $index) use ($bczTeam) {
            $user = User::factory()->create([
                'name' => $data['name'],
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

            return $user;
        });

        $athletes = User::factory(8)->create()->each(function (User $user, int $index) use ($bczTeam) {
            $user->assignRole(RoleEnum::CUSTOMER);
            $user->teams()->attach($bczTeam, ['role' => RoleEnum::ATHLETE->value, 'is_active' => true, 'joined_at' => now()->subMonths(rand(1, 24))]);
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
            $user->teams()->attach($bczTeam, ['role' => RoleEnum::ATHLETE->value, 'is_active' => true, 'joined_at' => now()->subMonths(rand(3, 12))]);

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
                'notify_on_available' => true,
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
                'notify_on_available' => true,
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
        Event::factory()->create([
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
        ]);

        // 2. Past corporate show
        Event::factory()->create([
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
        ]);

        // 3. Past lecture
        Event::factory()->create([
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
        ]);

        // 4. Past international workshop report
        Event::factory()->create([
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
        ]);

        // 5. Recent report — outdoor showcase
        Event::factory()->create([
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
        ]);

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
        ]);
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
                'status' => 'confirmed',
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
        ]);
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
                'status' => $i < 15 ? 'confirmed' : 'pending',
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
        ]);
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
        ]);
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
                'status' => 'confirmed',
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
        ]);
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
        ]);
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
        ]);
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
        ]);
        EventOrganization::factory()->paid(25.00)->create([
            'event_id' => $upcomingCompetition->id,
            'max_capacity' => 60,
            'registration_opens_at' => now()->subWeek(),
            'registration_closes_at' => now()->addMonth(),
            'is_public_registration' => true,
            'show_countdown' => true,
        ]);
        $upcomingCompDetail = CompetitionDetail::factory()->create(['event_id' => $upcomingCompetition->id]);

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
        ]);
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
        ]);
        EventOrganization::factory()->paid(40.00)->create([
            'event_id' => $futureCompetition->id,
            'max_capacity' => 120,
            'registration_opens_at' => now()->addMonth(),
            'registration_closes_at' => now()->addMonths(4),
            'is_public_registration' => true,
            'show_countdown' => true,
        ]);
        $futureCompDetail = CompetitionDetail::factory()->create(['event_id' => $futureCompetition->id]);

        $allCompDetails = collect([$pastCompDetail, $battleCompDetail, $upcomingCompDetail, $freeCompDetail, $futureCompDetail]);

        // Attach disciplines, categories, judges to all competitions
        $allCompDetails->each(function (CompetitionDetail $detail) use ($disciplines, $allCategories, $judges) {
            $detail->disciplines()->attach($disciplines->random(rand(3, 5))->pluck('id'));
            $detail->athleteCategories()->attach($allCategories->pluck('id'));

            $judges->each(function (User $judge) use ($detail, $disciplines) {
                $detail->judges()->attach($judge->id, [
                    'discipline_id' => $disciplines->random()->id,
                ]);
            });
        });

        // Registration fees — different pricing per competition
        $allCompDetails->each(function (CompetitionDetail $detail, $i) use ($allCategories) {
            $basePrice = [25.00, 15.00, 25.00, 0.00, 40.00][$i] ?? 20.00;

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
                'status' => 'confirmed',
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
                'status' => 'confirmed',
                'weight_in' => rand(60, 90) + (rand(0, 9) / 10),
                'registered_at' => now()->subMonths(2)->addDays($index),
            ]);
        });

        // Upcoming — mixed confirmed/pending
        $allCompetitors->take(12)->each(function (User $athlete, $index) use ($upcomingCompetition, $allCategories) {
            $category = $allCategories[$index % 3];
            EventRegistration::factory()->create([
                'event_id' => $upcomingCompetition->id,
                'user_id' => $athlete->id,
                'athlete_category_id' => $category->id,
                'status' => $index < 8 ? 'confirmed' : 'pending',
                'registered_at' => now()->subDays(7 - ($index % 7)),
            ]);
        });

        // Community jam — small group
        $allCompetitors->take(12)->each(function (User $athlete, $index) use ($freeCompetition, $allCategories) {
            EventRegistration::factory()->create([
                'event_id' => $freeCompetition->id,
                'user_id' => $athlete->id,
                'athlete_category_id' => $allCategories[$index % 3]->id,
                'status' => 'confirmed',
                'registered_at' => now()->subMonths(6)->addDays($index),
            ]);
        });

        // ===== COMPETITION 1: BCZ Championship — POINTS-BASED ROUNDS =====
        // Men: Qualification (Statics + Dynamics) → Final (Combos)
        $menQual = CompetitionRound::factory()->create([
            'competition_detail_id' => $pastCompDetail->id,
            'athlete_category_id' => $menCategory->id,
            'round_number' => 1,
            'name' => 'Kvalifikacia - Muzi',
            'scoring_format' => 'points',
            'advancement_type' => RoundAdvancementTypeEnum::TOP_BY_POINTS,
            'advance_count' => 6,
            'sort_order' => 1,
        ]);
        $menFinal = CompetitionRound::factory()->create([
            'competition_detail_id' => $pastCompDetail->id,
            'athlete_category_id' => $menCategory->id,
            'round_number' => 2,
            'name' => 'Finale - Muzi',
            'scoring_format' => 'points',
            'advancement_type' => RoundAdvancementTypeEnum::TOP_BY_POINTS,
            'advance_count' => 3,
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
            'advance_count' => 4,
            'sort_order' => 3,
        ]);
        $womenFinal = CompetitionRound::factory()->create([
            'competition_detail_id' => $pastCompDetail->id,
            'athlete_category_id' => $womenCategory->id,
            'round_number' => 2,
            'name' => 'Finale - Zeny',
            'scoring_format' => 'points',
            'advancement_type' => RoundAdvancementTypeEnum::TOP_BY_POINTS,
            'advance_count' => 3,
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
            'advance_count' => 3,
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

        // Men Qual results — 8 competitors, scored and ranked
        $menCompetitors = $allCompetitors->take(8);
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
            'battle_size' => 1,
            'sort_order' => 1,
        ]);
        $r16Winners = collect();
        for ($b = 0; $b < 8; $b++) {
            $a = $battleAthletes[$b * 2];
            $bComp = $battleAthletes[$b * 2 + 1];
            $winner = rand(0, 1) === 0 ? $a : $bComp;
            $r16Winners->push($winner);
            Battle::factory()->create([
                'competition_round_id' => $roundOf16->id,
                'athlete_category_id' => $menCategory->id,
                'bracket_position' => $b + 1,
                'competitor_a_id' => [$a->id, $a->name],
                'competitor_b_id' => [$bComp->id, $bComp->name],
                'winner_id' => [$winner->id, $winner->name],
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
            'battle_size' => 1,
            'sort_order' => 2,
        ]);
        $qfWinners = collect();
        for ($b = 0; $b < 4; $b++) {
            $a = $r16Winners[$b * 2];
            $bComp = $r16Winners[$b * 2 + 1];
            $winner = rand(0, 1) === 0 ? $a : $bComp;
            $qfWinners->push($winner);
            Battle::factory()->create([
                'competition_round_id' => $quarterFinals->id,
                'athlete_category_id' => $menCategory->id,
                'bracket_position' => $b + 1,
                'competitor_a_id' => [$a->id, $a->name],
                'competitor_b_id' => [$bComp->id, $bComp->name],
                'winner_id' => [$winner->id, $winner->name],
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
            'battle_size' => 1,
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
            Battle::factory()->create([
                'competition_round_id' => $semiFinals->id,
                'athlete_category_id' => $menCategory->id,
                'bracket_position' => $b + 1,
                'competitor_a_id' => [$a->id, $a->name],
                'competitor_b_id' => [$bComp->id, $bComp->name],
                'winner_id' => [$winner->id, $winner->name],
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
            'battle_size' => 1,
            'sort_order' => 4,
        ]);
        $thirdPlaceWinner = $sfLosers->random();
        Battle::factory()->create([
            'competition_round_id' => $thirdPlaceRound->id,
            'athlete_category_id' => $menCategory->id,
            'bracket_position' => 1,
            'competitor_a_id' => [$sfLosers[0]->id, $sfLosers[0]->name],
            'competitor_b_id' => [$sfLosers[1]->id, $sfLosers[1]->name],
            'winner_id' => [$thirdPlaceWinner->id, $thirdPlaceWinner->name],
        ]);

        // Grand Final
        $grandFinal = CompetitionRound::factory()->create([
            'competition_detail_id' => $battleCompDetail->id,
            'athlete_category_id' => $menCategory->id,
            'round_number' => 5,
            'name' => 'Finale',
            'scoring_format' => 'coach_decision',
            'advancement_type' => RoundAdvancementTypeEnum::BATTLE_WINNER,
            'battle_size' => 1,
            'sort_order' => 5,
        ]);
        $champion = $sfWinners->random();
        Battle::factory()->create([
            'competition_round_id' => $grandFinal->id,
            'athlete_category_id' => $menCategory->id,
            'bracket_position' => 1,
            'competitor_a_id' => [$sfWinners[0]->id, $sfWinners[0]->name],
            'competitor_b_id' => [$sfWinners[1]->id, $sfWinners[1]->name],
            'winner_id' => [$champion->id, $champion->name],
        ]);

        // ===== COMPETITION 4: Community Jam — simple points, no battles =====
        $jamRound = CompetitionRound::factory()->create([
            'competition_detail_id' => $freeCompDetail->id,
            'round_number' => 1,
            'name' => 'Freestyle Jam',
            'scoring_format' => 'points',
            'advancement_type' => RoundAdvancementTypeEnum::TOP_BY_POINTS,
            'advance_count' => 3,
            'sort_order' => 1,
        ]);
        $jamPart = RoundPart::factory()->create([
            'competition_round_id' => $jamRound->id,
            'name' => ['sk' => 'Freestyle', 'en' => 'Freestyle'],
            'duration_seconds' => 60,
            'sort_order' => 1,
        ]);
        $allCompetitors->take(12)->each(function (User $athlete, $index) use ($jamPart) {
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

        // Historical memberships for older season (all expired/completed)
        $athletes->take(4)->each(function (User $athlete) use ($bczTeam, $olderSeason) {
            Membership::create([
                'team_id' => $bczTeam->id,
                'user_id' => $athlete->id,
                'team_season_id' => $olderSeason->id,
                'status' => MembershipStatusEnum::EXPIRED,
                'fee_amount' => 40.00,
                'fee_currency' => 'EUR',
                'starts_at' => $olderSeason->starts_at,
                'ends_at' => $olderSeason->ends_at,
            ]);
        });

        // Past season memberships — mix of expired and cancelled
        $athletes->take(6)->each(function (User $athlete, $index) use ($bczTeam, $pastSeason) {
            $status = $index < 4 ? MembershipStatusEnum::EXPIRED : MembershipStatusEnum::CANCELLED;

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

        // Payments for memberships (completed)
        $memberships->where('status', MembershipStatusEnum::ACTIVE)->each(function (Membership $membership) use ($bczTeam) {
            $method = collect([PaymentMethodEnum::CASH, PaymentMethodEnum::BANK_TRANSFER, PaymentMethodEnum::CASH, PaymentMethodEnum::STRIPE])->random();

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

        // Payments for training registrations — varied methods and statuses
        $trainingRegistrations = TrainingRegistration::whereNotNull('user_id')
            ->whereHas('training', fn ($q) => $q->where('pricing_type', TrainingPricingTypeEnum::PAID))
            ->with('training')
            ->get();

        $paymentMethods = [
            PaymentMethodEnum::STRIPE,
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
                    'stripe_payment_id' => $method === PaymentMethodEnum::STRIPE ? 'pi_demo_'.fake()->regexify('[a-zA-Z0-9]{16}') : null,
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
        $compRegistrations = EventRegistration::where('status', 'confirmed')
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
                'payment_method' => PaymentMethodEnum::STRIPE,
                'stripe_payment_id' => 'pi_demo_refunded_'.fake()->regexify('[a-zA-Z0-9]{12}'),
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

        // Season + memberships for second team
        $secondTeamSeason = TeamSeason::create([
            'team_id' => $secondTeam->id,
            'name' => 'Sezóna '.now()->year,
            'starts_at' => now()->startOfYear()->month(1)->startOfMonth(),
            'ends_at' => now()->startOfYear()->month(12)->endOfMonth(),
            'fee_amount' => 120.00,
            'fee_currency' => 'EUR',
            'payment_deadline_days' => 14,
        ]);

        User::factory(3)->create()->each(function (User $user) use ($secondTeam, $secondTeamSeason) {
            $user->assignRole(RoleEnum::CUSTOMER);
            $user->teams()->attach($secondTeam, ['role' => RoleEnum::ATHLETE->value, 'is_active' => true, 'joined_at' => now()->subMonths(rand(1, 6))]);

            Membership::create([
                'team_id' => $secondTeam->id,
                'user_id' => $user->id,
                'team_season_id' => $secondTeamSeason->id,
                'status' => MembershipStatusEnum::ACTIVE,
                'fee_amount' => 120.00,
                'fee_currency' => 'EUR',
                'starts_at' => $secondTeamSeason->starts_at,
                'ends_at' => $secondTeamSeason->ends_at,
            ]);
        });
    }
}
