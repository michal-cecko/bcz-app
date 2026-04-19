<?php

namespace Tests\Feature;

use App\Models\CompetitionDetail;
use App\Models\Event;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CompetitionManagerTest extends TestCase
{
    use RefreshDatabase;

    public function test_competition_detail_can_have_manager(): void
    {
        $manager = User::factory()->create();
        $event = Event::factory()->competition()->create();
        $detail = CompetitionDetail::factory()->create([
            'event_id' => $event->id,
            'manager_id' => $manager->id,
        ]);

        $this->assertNotNull($detail->manager);
        $this->assertTrue($detail->manager->is($manager));
    }

    public function test_competition_detail_manager_is_nullable(): void
    {
        $event = Event::factory()->competition()->create();
        $detail = CompetitionDetail::factory()->create([
            'event_id' => $event->id,
            'manager_id' => null,
        ]);

        $this->assertNull($detail->manager);
    }

    public function test_manager_deletion_nullifies_competition_detail(): void
    {
        $manager = User::factory()->create();
        $event = Event::factory()->competition()->create();
        $detail = CompetitionDetail::factory()->create([
            'event_id' => $event->id,
            'manager_id' => $manager->id,
        ]);

        $manager->forceDelete();

        $detail->refresh();
        $this->assertNull($detail->manager_id);
    }
}
