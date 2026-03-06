<?php

namespace Database\Seeders;

use App\Enums\RoleEnum;
use BezhanSalleh\FilamentShield\Support\Utils;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Seed the application roles.
     */
    public function run(): void
    {
        $guardName = Utils::getFilamentAuthGuard();

        foreach (RoleEnum::cases() as $role) {
            Role::firstOrCreate([
                'name' => $role->value,
                'guard_name' => $guardName,
            ]);
        }
    }
}
