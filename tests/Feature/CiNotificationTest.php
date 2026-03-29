<?php

namespace Tests\Feature;

use Tests\TestCase;

class CiNotificationTest extends TestCase
{
    public function test_deliberate_failure_to_test_telegram(): void
    {
        $this->assertTrue(false, 'Deliberate failure to test Telegram notification');
    }
}
