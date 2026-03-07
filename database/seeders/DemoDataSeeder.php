<?php

namespace Database\Seeders;

use App\Enums\CoachRoleEnum;
use App\Enums\ComplexityLevelEnum;
use App\Enums\GenderEnum;
use App\Enums\InquiryReasonEnum;
use App\Enums\InquiryStatusEnum;
use App\Enums\RoleEnum;
use App\Enums\RoundAdvancementTypeEnum;
use App\Enums\ScoringFormatEnum;
use App\Enums\SponsorTagEnum;
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
use App\Models\RegistrationFee;
use App\Models\RoundPart;
use App\Models\Sponsor;
use App\Models\SportCategory;
use App\Models\Team;
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

        $sportCategories = SportCategory::all();
        $parkour = $sportCategories->firstWhere('slug', 'parkour-freerunning');
        $streetWorkout = $sportCategories->firstWhere('slug', 'street-workout');

        // --- Users with roles ---
        $coaches = User::factory(3)->create()->each(function (User $user) use ($bczTeam) {
            $user->assignRole(RoleEnum::COACH);
            $user->teams()->attach($bczTeam, ['is_active' => true, 'joined_at' => now()->subMonths(rand(6, 36))]);
            CoachProfile::factory()->create(['user_id' => $user->id]);
        });

        $athletes = User::factory(8)->create()->each(function (User $user) use ($bczTeam) {
            $user->assignRole(RoleEnum::ATHLETE);
            $user->teams()->attach($bczTeam, ['is_active' => true, 'joined_at' => now()->subMonths(rand(1, 24))]);
            AthleteProfile::factory()->create(['user_id' => $user->id]);
        });

        $judges = User::factory(2)->create()->each(function (User $user) use ($bczTeam) {
            $user->assignRole(RoleEnum::JUDGE);
            $user->teams()->attach($bczTeam, ['is_active' => true, 'joined_at' => now()->subMonths(rand(3, 12))]);
        });

        $customers = User::factory(5)->create()->each(function (User $user) use ($bczTeam) {
            $user->assignRole(RoleEnum::CUSTOMER);
            $user->teams()->attach($bczTeam, ['is_active' => true, 'joined_at' => now()->subMonths(rand(1, 6))]);
        });

        // --- Media Library Folder Structure ---
        $folderService = app(MediaLibraryFolderService::class);
        $allUsers = $coaches->merge($athletes)->merge($judges)->merge($customers);
        $allUsers->each(function (User $user) use ($folderService) {
            $folderService->ensureUserFolders($user);
        });

        // --- Exercise Categories & Exercises ---
        $exerciseCategories = collect([
            ['name' => ['sk' => 'Skoky', 'en' => 'Jumps'], 'description' => ['sk' => 'Rôzne typy skokov']],
            ['name' => ['sk' => 'Vaults', 'en' => 'Vaults'], 'description' => ['sk' => 'Prekonávanie prekážok']],
            ['name' => ['sk' => 'Statické prvky', 'en' => 'Static Holds'], 'description' => ['sk' => 'Statické silové prvky']],
            ['name' => ['sk' => 'Dynamické prvky', 'en' => 'Dynamic Moves'], 'description' => ['sk' => 'Dynamické pohyby']],
        ])->map(function ($data, $index) use ($parkour, $streetWorkout) {
            $cat = ExerciseCategory::factory()->create(array_merge($data, ['sort_order' => $index]));
            $cat->sportCategories()->attach($index < 2 ? $parkour : $streetWorkout);

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

        // --- Trainings ---
        $trainings = collect([
            [
                'title' => ['sk' => 'Parkour pre začiatočníkov', 'en' => 'Parkour for Beginners'],
                'sport_category_id' => $parkour->id,
                'pricing_type' => TrainingPricingTypeEnum::FREE,
                'age_group' => '10-16',
            ],
            [
                'title' => ['sk' => 'Street Workout Advanced', 'en' => 'Street Workout Advanced'],
                'sport_category_id' => $streetWorkout->id,
                'pricing_type' => TrainingPricingTypeEnum::PAID,
                'price_amount' => 15.00,
                'age_group' => '16+',
            ],
            [
                'title' => ['sk' => 'Freerunning trénink', 'en' => 'Freerunning Training'],
                'sport_category_id' => $parkour->id,
                'pricing_type' => TrainingPricingTypeEnum::MEMBERSHIP_REQUIRED,
                'age_group' => '14-25',
            ],
        ])->map(function ($data) use ($bczTeam) {
            return Training::factory()->create(array_merge($data, [
                'team_id' => $bczTeam->id,
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

        // Training registrations
        $customers->each(function (User $customer) use ($trainings) {
            TrainingRegistration::factory()->create([
                'training_id' => $trainings->random()->id,
                'user_id' => $customer->id,
            ]);
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
                'name' => ['sk' => 'Speed Run', 'en' => 'Speed Run'],
                'description' => ['sk' => 'Rýchlostná disciplína cez prekážkovú dráhu.'],
                'scoring_format' => ScoringFormatEnum::POINTS,
                'sort_order' => 1,
            ],
            [
                'name' => ['sk' => 'Freestyle', 'en' => 'Freestyle'],
                'description' => ['sk' => 'Voľná zostava s hodnotením tvorivosti a techniky.'],
                'scoring_format' => ScoringFormatEnum::POINTS,
                'sort_order' => 2,
            ],
            [
                'name' => ['sk' => 'Battle', 'en' => 'Battle'],
                'description' => ['sk' => '1v1 súboj hodnotený rozhodcami.'],
                'scoring_format' => ScoringFormatEnum::COACH_DECISION,
                'sort_order' => 3,
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
        Sponsor::factory()->create([
            'name' => 'Red Bull',
            'tag' => SponsorTagEnum::MAIN_SPONSOR,
            'link' => 'https://www.redbull.com/sk-sk',
            'is_visible' => true,
            'sort_order' => 1,
        ]);
        Sponsor::factory()->create([
            'name' => 'Nike',
            'tag' => SponsorTagEnum::MAIN_SPONSOR,
            'link' => 'https://www.nike.com',
            'is_visible' => true,
            'sort_order' => 2,
        ]);
        Sponsor::factory()->create([
            'name' => 'Denník N',
            'tag' => SponsorTagEnum::MEDIAL_SPONSOR,
            'link' => 'https://dennikn.sk',
            'is_visible' => true,
            'sort_order' => 3,
        ]);
        Sponsor::factory()->create([
            'name' => 'Město Bratislava',
            'tag' => SponsorTagEnum::PARTNER,
            'link' => 'https://bratislava.sk',
            'is_visible' => true,
            'sort_order' => 4,
        ]);
        Sponsor::factory()->create([
            'name' => 'Decathlon',
            'tag' => SponsorTagEnum::SUPPORTER,
            'link' => 'https://www.decathlon.sk',
            'is_visible' => true,
            'sort_order' => 5,
        ]);
        Sponsor::factory()->create([
            'name' => 'GymBeam',
            'tag' => SponsorTagEnum::SUPPORTER,
            'link' => 'https://www.gymbeam.sk',
            'is_visible' => false,
            'sort_order' => 6,
        ]);
    }
}
