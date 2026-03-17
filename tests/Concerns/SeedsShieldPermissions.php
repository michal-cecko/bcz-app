<?php

namespace Tests\Concerns;

use Database\Seeders\ShieldPermissionSeeder;

trait SeedsShieldPermissions
{
    protected function seedShieldPermissions(): void
    {
        $this->seed(ShieldPermissionSeeder::class);
    }
}
