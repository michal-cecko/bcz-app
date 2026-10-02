<?php

namespace Tests\Feature\Observers;

use App\Models\Training;
use App\Models\TrainingWaitlist;
use App\Models\User;
use App\Notifications\TrainingSpotAvailable;
use Closure;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Notifications\Events\NotificationSent;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Notification;
use RuntimeException;
use Tests\TestCase;

class TrainingObserverTest extends TestCase
{
    use RefreshDatabase;

    private Training $training;

    private User $waitingUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->training = Training::factory()->create([
            'max_capacity' => 1,
            'notify_on_available' => true,
        ]);
        $this->waitingUser = User::factory()->create();

        TrainingWaitlist::create([
            'training_id' => $this->training->id,
            'user_id' => $this->waitingUser->id,
            'created_at' => now(),
        ]);
    }

    public function test_raising_capacity_saves_and_notifies_the_waitlist(): void
    {
        Notification::fake();

        $this->training->update(['max_capacity' => 5]);

        $this->assertSame(5, $this->training->fresh()->max_capacity);
        Notification::assertSentTo($this->waitingUser, TrainingSpotAvailable::class);
        $this->assertSame(0, $this->training->waitlistEntries()->count());
    }

    public function test_removing_the_capacity_limit_notifies_the_waitlist(): void
    {
        Notification::fake();

        $this->training->update(['max_capacity' => null]);

        $this->assertNull($this->training->fresh()->max_capacity);
        Notification::assertSentTo($this->waitingUser, TrainingSpotAvailable::class);
    }

    public function test_lowering_capacity_does_not_notify_the_waitlist(): void
    {
        $this->training->updateQuietly(['max_capacity' => 5]);
        Notification::fake();

        $this->training->update(['max_capacity' => 2]);

        $this->assertSame(2, $this->training->fresh()->max_capacity);
        Notification::assertNothingSent();
        $this->assertSame(1, $this->training->waitlistEntries()->count());
    }

    public function test_saving_other_fields_does_not_notify_the_waitlist(): void
    {
        Notification::fake();

        $this->training->update(['notify_on_available' => false]);
        $this->training->update(['notify_on_available' => true]);

        Notification::assertNothingSent();
        $this->assertSame(1, $this->training->waitlistEntries()->count());
    }

    public function test_waitlist_is_not_notified_when_the_save_rolls_back(): void
    {
        $sentCount = $this->countSentNotifications();

        try {
            DB::transaction(function (): void {
                $this->training->update(['max_capacity' => 5]);

                throw new RuntimeException('A later step of the save failed.');
            });
        } catch (RuntimeException) {
        }

        $this->assertSame(1, $this->training->fresh()->max_capacity);
        $this->assertSame(0, $sentCount());
        $this->assertSame(1, $this->training->waitlistEntries()->count());
    }

    public function test_waitlist_is_notified_once_the_save_commits(): void
    {
        $sentCount = $this->countSentNotifications();

        DB::transaction(fn () => $this->training->update(['max_capacity' => 5]));

        $this->assertSame(2, $sentCount());
    }

    /**
     * NotificationSent fires per channel (mail + database) and is not undone by a rollback,
     * unlike the database notification row.
     *
     * @return Closure(): int
     */
    private function countSentNotifications(): Closure
    {
        $count = 0;
        Event::listen(NotificationSent::class, function () use (&$count): void {
            $count++;
        });

        return function () use (&$count): int {
            return $count;
        };
    }
}
