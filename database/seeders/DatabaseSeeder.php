<?php

namespace Database\Seeders;

use App\Enums\RoleEnum;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(RoleSeeder::class);

        $superAdmin = User::factory()->create([
            'name' => 'Super Admin',
            'email' => 'ceckomichal@gmail.com',
        ]);

        $superAdmin->assignRole(RoleEnum::SuperAdmin);
    }
}
