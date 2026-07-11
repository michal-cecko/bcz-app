<?php

namespace Tests\Feature\Console;

use App\Enums\RegistrationFieldTypeEnum;
use App\Models\AthleteCategory;
use App\Models\CompetitionDetail;
use App\Models\CompetitionRound;
use App\Models\Event;
use App\Models\EventRegistration;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class SplitSharedRegistrationAccountTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_splits_extra_registrations_into_their_own_distinct_users(): void
    {
        $owner = User::factory()->create([
            'first_name' => 'Nikola',
            'last_name' => 'Coufalová',
            'email' => 'coufii6@gmail.com',
        ]);

        $event = Event::factory()->competition()->create();
        $detail = CompetitionDetail::factory()->create(['event_id' => $event->id]);
        $catA = AthleteCategory::factory()->create();
        $catB = AthleteCategory::factory()->create();

        $own = $this->makeRegistration($owner, $event, $catA, 'Nikola', 'Coufalová', Carbon::now()->subMinutes(30));
        $r1 = $this->makeRegistration($owner, $event, $catB, 'Samuel', 'Ivan', Carbon::now()->subMinutes(20));
        $r2 = $this->makeRegistration($owner, $event, $catB, 'Simon', 'Toráč', Carbon::now()->subMinutes(10));

        // A round whose competitor_order collapsed both catB athletes onto the shared id.
        $round = CompetitionRound::factory()->create([
            'competition_detail_id' => $detail->id,
            'athlete_category_id' => $catB->id,
            'competitor_order' => [$owner->id],
        ]);

        $this->artisan('registrations:split-account', ['user' => $owner->id])->assertSuccessful();

        // The account holder keeps only their own registration.
        $this->assertSame($owner->id, $own->fresh()->user_id);
        $this->assertSame(1, EventRegistration::where('user_id', $owner->id)->count());

        // The two other athletes now have their own, distinct users — names preserved.
        $u1 = $r1->fresh()->user;
        $u2 = $r2->fresh()->user;
        $this->assertNotSame($owner->id, $u1->id);
        $this->assertNotSame($owner->id, $u2->id);
        $this->assertNotSame($u1->id, $u2->id);
        $this->assertSame('Samuel Ivan', $u1->name);
        $this->assertSame('Simon Toráč', $u2->name);

        // Emails are unique and plus-addressed off the base (still reach the same inbox).
        $this->assertStringStartsWith('coufii6+', $u1->email);
        $this->assertStringEndsWith('@gmail.com', $u1->email);
        $this->assertNotSame($u1->email, $u2->email);

        // competitor_order no longer references the shared id; it now lists the two new users.
        $order = $round->fresh()->competitor_order;
        $this->assertNotContains($owner->id, $order);
        $this->assertContains($u1->id, $order);
        $this->assertContains($u2->id, $order);
    }

    public function test_dry_run_writes_nothing(): void
    {
        $owner = User::factory()->create(['email' => 'coach@example.com']);
        $event = Event::factory()->competition()->create();
        $category = AthleteCategory::factory()->create();

        $this->makeRegistration($owner, $event, $category, 'Kid', 'One', Carbon::now()->subMinutes(20));
        $this->makeRegistration($owner, $event, $category, 'Kid', 'Two', Carbon::now()->subMinutes(10));

        $usersBefore = User::count();

        $this->artisan('registrations:split-account', ['user' => $owner->id, '--dry-run' => true])->assertSuccessful();

        $this->assertSame($usersBefore, User::count());
        $this->assertSame(2, EventRegistration::where('user_id', $owner->id)->count());
    }

    private function makeRegistration(User $user, Event $event, AthleteCategory $category, string $first, string $last, Carbon $registeredAt): EventRegistration
    {
        $registration = EventRegistration::factory()->approved()->create([
            'event_id' => $event->id,
            'user_id' => $user->id,
            'athlete_category_id' => $category->id,
            'registered_at' => $registeredAt,
        ]);

        $registration->fieldValues()->createMany([
            ['field_key' => 'meno', 'field_type' => RegistrationFieldTypeEnum::FIRST_NAME, 'value' => $first],
            ['field_key' => 'priezvisko', 'field_type' => RegistrationFieldTypeEnum::LAST_NAME, 'value' => $last],
        ]);

        return $registration;
    }
}
