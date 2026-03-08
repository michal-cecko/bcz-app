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

        if ($bczTeam) {
            $superAdmin->teams()->attach($bczTeam, [
                'is_active' => true,
                'joined_at' => now(),
            ]);
        }

        $this->call(DemoDataSeeder::class);
    }
}
