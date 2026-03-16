<?php

use BezhanSalleh\FilamentShield\Support\Utils;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $guardName = Utils::getFilamentAuthGuard();

        $customerRole = Role::firstOrCreate([
            'name' => 'CUSTOMER',
            'guard_name' => $guardName,
        ]);

        $athleteRole = Role::where('name', 'ATHLETE')
            ->where('guard_name', $guardName)
            ->first();

        if (! $athleteRole) {
            return;
        }

        // Find users with ATHLETE role who don't belong to any team
        $teamlessAthleteUserIds = DB::table('model_has_roles')
            ->where('role_id', $athleteRole->id)
            ->whereNotIn('model_id', function ($query) {
                $query->select('user_id')->from('team_user');
            })
            ->pluck('model_id');

        if ($teamlessAthleteUserIds->isEmpty()) {
            return;
        }

        // Replace ATHLETE with CUSTOMER for teamless users
        DB::table('model_has_roles')
            ->where('role_id', $athleteRole->id)
            ->whereIn('model_id', $teamlessAthleteUserIds)
            ->update(['role_id' => $customerRole->id]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $guardName = Utils::getFilamentAuthGuard();

        $customerRole = Role::where('name', 'CUSTOMER')
            ->where('guard_name', $guardName)
            ->first();

        $athleteRole = Role::where('name', 'ATHLETE')
            ->where('guard_name', $guardName)
            ->first();

        if ($customerRole && $athleteRole) {
            DB::table('model_has_roles')
                ->where('role_id', $customerRole->id)
                ->update(['role_id' => $athleteRole->id]);
        }

        if ($customerRole) {
            $customerRole->delete();
        }
    }
};
