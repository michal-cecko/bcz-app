<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Migrate OWNER role assignments to ADMIN
        $ownerRole = DB::table('roles')->where('name', 'OWNER')->first();
        $adminRole = DB::table('roles')->where('name', 'ADMIN')->first();

        if ($ownerRole && $adminRole) {
            $ownerUserIds = DB::table('model_has_roles')
                ->where('role_id', $ownerRole->id)
                ->pluck('model_id');

            $existingAdminIds = DB::table('model_has_roles')
                ->where('role_id', $adminRole->id)
                ->whereIn('model_id', $ownerUserIds)
                ->pluck('model_id');

            $toMigrate = $ownerUserIds->diff($existingAdminIds);

            foreach ($toMigrate as $userId) {
                DB::table('model_has_roles')->insert([
                    'role_id' => $adminRole->id,
                    'model_type' => 'App\\Models\\User',
                    'model_id' => $userId,
                ]);
            }

            DB::table('model_has_roles')->where('role_id', $ownerRole->id)->delete();
            DB::table('role_has_permissions')->where('role_id', $ownerRole->id)->delete();
            DB::table('roles')->where('id', $ownerRole->id)->delete();
        }

        // Migrate CUSTOMER role assignments to ATHLETE
        $customerRole = DB::table('roles')->where('name', 'CUSTOMER')->first();
        $athleteRole = DB::table('roles')->where('name', 'ATHLETE')->first();

        if ($customerRole && $athleteRole) {
            $customerUserIds = DB::table('model_has_roles')
                ->where('role_id', $customerRole->id)
                ->pluck('model_id');

            $existingAthleteIds = DB::table('model_has_roles')
                ->where('role_id', $athleteRole->id)
                ->whereIn('model_id', $customerUserIds)
                ->pluck('model_id');

            $toMigrate = $customerUserIds->diff($existingAthleteIds);

            foreach ($toMigrate as $userId) {
                DB::table('model_has_roles')->insert([
                    'role_id' => $athleteRole->id,
                    'model_type' => 'App\\Models\\User',
                    'model_id' => $userId,
                ]);
            }

            DB::table('model_has_roles')->where('role_id', $customerRole->id)->delete();
            DB::table('role_has_permissions')->where('role_id', $customerRole->id)->delete();
            DB::table('roles')->where('id', $customerRole->id)->delete();
        }

        // Add public profile fields to users table
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('has_public_profile')->default(false)->after('locale');
            $table->timestamp('public_profile_approved_at')->nullable()->after('has_public_profile');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['has_public_profile', 'public_profile_approved_at']);
        });

        DB::table('roles')->insert([
            ['name' => 'OWNER', 'guard_name' => 'web', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'CUSTOMER', 'guard_name' => 'web', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
};
