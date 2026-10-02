<?php

namespace Tests\Feature\Filament;

use Filament\Notifications\Livewire\Notifications;
use Livewire\Livewire;
use Tests\TestCase;

class NotificationsHydrationTest extends TestCase
{
    public function test_client_update_with_non_array_items_is_ignored(): void
    {
        Livewire::test(Notifications::class)
            ->set('notifications', [7])
            ->assertOk()
            ->assertCount('notifications', 0);
    }
}
