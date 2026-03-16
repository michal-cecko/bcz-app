<?php

use App\Enums\RoleEnum;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Drop the unique constraint so a user can have multiple rows per team (one per role)
        Schema::table('team_user', function (Blueprint $table) {
            $table->dropUnique(['team_id', 'user_id']);
        });

        // 2. Add nullable role column
        Schema::table('team_user', function (Blueprint $table) {
            $table->string('role')->nullable()->after('user_id');
        });

        // 3. Backfill: for each user with a team-scoped Spatie role AND a team_user row,
        //    set the pivot role. Users without any team-scoped Spatie role default to ATHLETE.
        $teamScopedRoleValues = collect(RoleEnum::teamScopedCases())->pluck('value');

        $roleIds = DB::table('roles')
            ->whereIn('name', $teamScopedRoleValues)
            ->pluck('id', 'name');

        $pivotRows = DB::table('team_user')->get();

        foreach ($pivotRows as $pivot) {
            $userRoles = DB::table('model_has_roles')
                ->where('model_id', $pivot->user_id)
                ->where('model_type', 'App\\Models\\User')
                ->whereIn('role_id', $roleIds->values())
                ->pluck('role_id');

            if ($userRoles->isEmpty()) {
                // User is in a team but has no team-scoped Spatie role — default to ATHLETE
                DB::table('team_user')
                    ->where('id', $pivot->id)
                    ->update(['role' => RoleEnum::ATHLETE->value]);
            } else {
                $roleNames = $roleIds->filter(fn ($id) => $userRoles->contains($id))->keys();
                $firstName = true;

                foreach ($roleNames as $roleName) {
                    if ($firstName) {
                        DB::table('team_user')
                            ->where('id', $pivot->id)
                            ->update(['role' => $roleName]);
                        $firstName = false;
                    } else {
                        DB::table('team_user')->insert([
                            'team_id' => $pivot->team_id,
                            'user_id' => $pivot->user_id,
                            'role' => $roleName,
                            'is_active' => $pivot->is_active,
                            'joined_at' => $pivot->joined_at,
                            'created_at' => $pivot->created_at,
                            'updated_at' => now(),
                        ]);
                    }
                }
            }
        }

        // 4. Make role NOT NULL with default, add unique constraint
        Schema::table('team_user', function (Blueprint $table) {
            $table->string('role')->default(RoleEnum::ATHLETE->value)->nullable(false)->change();
            $table->unique(['team_id', 'user_id', 'role']);
        });

        // 5. Remove team-scoped roles from Spatie's model_has_roles
        DB::table('model_has_roles')
            ->whereIn('role_id', $roleIds->values())
            ->delete();
    }

    public function down(): void
    {
        // Re-assign team-scoped roles back to Spatie
        $roleIds = DB::table('roles')
            ->whereIn('name', collect(RoleEnum::teamScopedCases())->pluck('value'))
            ->pluck('id', 'name');

        $pivotRows = DB::table('team_user')->get();
        $processed = [];

        foreach ($pivotRows as $pivot) {
            $key = $pivot->user_id.'-'.$pivot->role;
            if (isset($processed[$key])) {
                continue;
            }
            $processed[$key] = true;

            $roleId = $roleIds->get($pivot->role);
            if ($roleId) {
                DB::table('model_has_roles')->insertOrIgnore([
                    'role_id' => $roleId,
                    'model_type' => 'App\\Models\\User',
                    'model_id' => $pivot->user_id,
                ]);
            }
        }

        Schema::table('team_user', function (Blueprint $table) {
            $table->dropUnique(['team_id', 'user_id', 'role']);
        });

        Schema::table('team_user', function (Blueprint $table) {
            $table->dropColumn('role');
        });

        // Remove duplicate team_user rows (keep the first one per team_id+user_id)
        $duplicates = DB::table('team_user')
            ->select('team_id', 'user_id', DB::raw('MIN(id) as keep_id'))
            ->groupBy('team_id', 'user_id')
            ->having(DB::raw('COUNT(*)'), '>', 1)
            ->get();

        foreach ($duplicates as $dup) {
            DB::table('team_user')
                ->where('team_id', $dup->team_id)
                ->where('user_id', $dup->user_id)
                ->where('id', '!=', $dup->keep_id)
                ->delete();
        }

        Schema::table('team_user', function (Blueprint $table) {
            $table->unique(['team_id', 'user_id']);
        });
    }
};
