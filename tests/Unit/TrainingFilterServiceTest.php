<?php

namespace Tests\Unit;

use App\Enums\GenderEnum;
use App\Models\Training;
use App\Models\User;
use App\Services\TrainingFilterService;
use Carbon\Carbon;
use Tests\TestCase;

class TrainingFilterServiceTest extends TestCase
{
    protected TrainingFilterService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new TrainingFilterService;
    }

    protected function makeUser(?string $birthDate = null, ?GenderEnum $gender = null): User
    {
        $user = new User;
        $user->birth_date = $birthDate ? Carbon::parse($birthDate) : null;
        $user->gender = $gender;

        return $user;
    }

    protected function makeTraining(?int $minAge = null, ?int $maxAge = null, ?GenderEnum $gender = null): Training
    {
        $training = new Training;
        $training->min_age = $minAge;
        $training->max_age = $maxAge;
        $training->gender = $gender;

        return $training;
    }

    public function test_matches_age_within_range(): void
    {
        Carbon::setTestNow('2026-03-17');
        $user = $this->makeUser('2018-01-01'); // 8 years old
        $training = $this->makeTraining(6, 10);

        $this->assertTrue($this->service->matchesAge($training, $user));
    }

    public function test_does_not_match_outside_age_range(): void
    {
        Carbon::setTestNow('2026-03-17');
        $user = $this->makeUser('2010-01-01'); // 16 years old
        $training = $this->makeTraining(6, 10);

        $this->assertFalse($this->service->matchesAge($training, $user));
    }

    public function test_matches_min_age_only(): void
    {
        Carbon::setTestNow('2026-03-17');
        $user = $this->makeUser('2000-01-01'); // 26 years old
        $training = $this->makeTraining(18, null); // 18+

        $this->assertTrue($this->service->matchesAge($training, $user));
    }

    public function test_does_not_match_min_age_when_too_young(): void
    {
        Carbon::setTestNow('2026-03-17');
        $user = $this->makeUser('2012-01-01'); // 14 years old
        $training = $this->makeTraining(18, null);

        $this->assertFalse($this->service->matchesAge($training, $user));
    }

    public function test_matches_max_age_only(): void
    {
        Carbon::setTestNow('2026-03-17');
        $user = $this->makeUser('2018-01-01'); // 8 years old
        $training = $this->makeTraining(null, 12); // up to 12

        $this->assertTrue($this->service->matchesAge($training, $user));
    }

    public function test_does_not_match_max_age_when_too_old(): void
    {
        Carbon::setTestNow('2026-03-17');
        $user = $this->makeUser('2000-01-01'); // 26 years old
        $training = $this->makeTraining(null, 12);

        $this->assertFalse($this->service->matchesAge($training, $user));
    }

    public function test_no_age_restriction_matches_all(): void
    {
        Carbon::setTestNow('2026-03-17');
        $user = $this->makeUser('2000-01-01');
        $training = $this->makeTraining(null, null);

        $this->assertTrue($this->service->matchesAge($training, $user));
    }

    public function test_null_user_birth_date_matches_all(): void
    {
        $user = $this->makeUser(null);
        $training = $this->makeTraining(6, 10);

        $this->assertTrue($this->service->matchesAge($training, $user));
    }

    public function test_matches_exact_min_age(): void
    {
        Carbon::setTestNow('2026-03-17');
        $user = $this->makeUser('2016-03-17'); // exactly 10
        $training = $this->makeTraining(10, 14);

        $this->assertTrue($this->service->matchesAge($training, $user));
    }

    public function test_matches_exact_max_age(): void
    {
        Carbon::setTestNow('2026-03-17');
        $user = $this->makeUser('2012-03-17'); // exactly 14
        $training = $this->makeTraining(10, 14);

        $this->assertTrue($this->service->matchesAge($training, $user));
    }

    public function test_matches_same_gender(): void
    {
        $user = $this->makeUser(null, GenderEnum::MALE);
        $training = $this->makeTraining(null, null, GenderEnum::MALE);

        $this->assertTrue($this->service->matchesGender($training, $user));
    }

    public function test_does_not_match_different_gender(): void
    {
        $user = $this->makeUser(null, GenderEnum::MALE);
        $training = $this->makeTraining(null, null, GenderEnum::FEMALE);

        $this->assertFalse($this->service->matchesGender($training, $user));
    }

    public function test_null_training_gender_matches_all(): void
    {
        $user = $this->makeUser(null, GenderEnum::MALE);
        $training = $this->makeTraining(null, null, null);

        $this->assertTrue($this->service->matchesGender($training, $user));
    }

    public function test_null_user_gender_matches_all(): void
    {
        $user = $this->makeUser(null, null);
        $training = $this->makeTraining(null, null, GenderEnum::FEMALE);

        $this->assertTrue($this->service->matchesGender($training, $user));
    }

    public function test_matches_user_profile_combines_checks(): void
    {
        Carbon::setTestNow('2026-03-17');
        $user = $this->makeUser('2018-01-01', GenderEnum::MALE); // 8yo male
        $training = $this->makeTraining(6, 10, GenderEnum::MALE);

        $this->assertTrue($this->service->matchesUserProfile($training, $user));
    }

    public function test_fails_profile_when_gender_mismatches(): void
    {
        Carbon::setTestNow('2026-03-17');
        $user = $this->makeUser('2018-01-01', GenderEnum::MALE);
        $training = $this->makeTraining(6, 10, GenderEnum::FEMALE);

        $this->assertFalse($this->service->matchesUserProfile($training, $user));
    }

    public function test_fails_profile_when_age_mismatches(): void
    {
        Carbon::setTestNow('2026-03-17');
        $user = $this->makeUser('2000-01-01', GenderEnum::MALE); // 26yo
        $training = $this->makeTraining(6, 10, GenderEnum::MALE);

        $this->assertFalse($this->service->matchesUserProfile($training, $user));
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }
}
