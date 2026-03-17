<?php

namespace Database\Seeders;

use App\Enums\RoleEnum;
use App\Models\Team;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(RoleSeeder::class);
        $this->call(TeamSeeder::class);
        $this->call(SportCategorySeeder::class);
        $this->call(SettingSeeder::class);
        $this->call(CurrencySeeder::class);
        $this->call(SubscriptionPlanSeeder::class);
        $this->call(PageSeeder::class);
        $this->call(MenuSeeder::class);

        $superAdmin = User::firstOrCreate(
            ['email' => 'ceckomichal@gmail.com'],
            [
                'name' => 'Super Admin',
                'first_name' => 'Super',
                'last_name' => 'Admin',
                'password' => bcrypt('password'),
            ],
        );

        $superAdmin->assignRole(RoleEnum::SUPER_ADMIN);

        $bczTeam = Team::query()->where('slug', 'bcz-club')->first();

        if ($bczTeam && ! $superAdmin->teams()->where('teams.id', $bczTeam->id)->wherePivot('role', RoleEnum::TEAM_ADMIN->value)->exists()) {
            $superAdmin->teams()->attach($bczTeam, [
                'role' => RoleEnum::TEAM_ADMIN->value,
                'is_active' => true,
                'joined_at' => now(),
            ]);
        }

        $admin = User::firstOrCreate(
            ['email' => 'admin@bczclub.com'],
            [
                'name' => 'Admin User',
                'first_name' => 'Admin',
                'last_name' => 'User',
                'password' => bcrypt('password'),
            ],
        );

        $admin->assignRole(RoleEnum::ADMIN);

        if ($bczTeam && ! $admin->teams()->where('teams.id', $bczTeam->id)->wherePivot('role', RoleEnum::TEAM_ADMIN->value)->exists()) {
            $admin->teams()->attach($bczTeam, [
                'role' => RoleEnum::TEAM_ADMIN->value,
                'is_active' => true,
                'joined_at' => now(),
            ]);
        }

        $this->call(ShieldPermissionSeeder::class);
        $this->call(DemoDataSeeder::class);
    }
}
