<?php

namespace Tests\Feature\Events;

use App\Livewire\EventsArchive;
use App\Models\Event;
use App\Models\EventCategory;
use App\Models\Setting;
use App\Models\Team;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class EventsArchiveFilterTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Regression test for a Sentry-reported 500: a bot probed `/eventy?kategoria=...`
     * with a malformed value (a UUID with extra trailing characters appended). Since
     * `event_category_id` is a native Postgres uuid column, the malformed string used
     * to reach the query builder unvalidated and blow up with SQLSTATE[22P02] instead
     * of just being ignored as "no filter".
     */
    public function test_malformed_category_filter_is_ignored_instead_of_erroring(): void
    {
        $team = Team::factory()->create();
        Setting::set('default_team_id', $team->id);

        $category = EventCategory::factory()->create();
        $event = Event::factory()->create([
            'team_id' => $team->id,
            'event_category_id' => $category->id,
            'is_published' => true,
        ]);

        Livewire::test(EventsArchive::class)
            ->set('categoryFilter', "{$category->id}'123")
            ->assertOk()
            ->assertSet('categoryFilter', '')
            ->assertSee($event->title);
    }
}
