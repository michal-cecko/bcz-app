<?php

namespace Tests\Feature;

use Tests\TestCase;

class CiNotificationTest extends TestCase
{
    public function test_deliberate_failure_to_test_telegram_group(): void
    {
        $this->assertTrue(false, 'Testing Telegram group notification');
    }
}
