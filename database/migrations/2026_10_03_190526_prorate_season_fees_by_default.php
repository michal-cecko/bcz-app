<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Seasons charge a member joining mid-season only for the months left,
     * so every season, existing ones included, switches prorating on.
     */
    public function up(): void
    {
        Schema::table('team_seasons', function (Blueprint $table) {
            $table->boolean('prorate_fee')->default(true)->change();
        });

        DB::table('team_seasons')->update(['prorate_fee' => true]);
    }

    public function down(): void
    {
        Schema::table('team_seasons', function (Blueprint $table) {
            $table->boolean('prorate_fee')->default(false)->change();
        });
    }
};
