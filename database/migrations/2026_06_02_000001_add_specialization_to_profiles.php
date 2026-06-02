<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('coach_profiles', function (Blueprint $table) {
            $table->json('specialization')->nullable()->after('biography');
        });

        Schema::table('athlete_profiles', function (Blueprint $table) {
            $table->json('specialization')->nullable()->after('journey_text');
        });
    }

    public function down(): void
    {
        Schema::table('coach_profiles', function (Blueprint $table) {
            $table->dropColumn('specialization');
        });

        Schema::table('athlete_profiles', function (Blueprint $table) {
            $table->dropColumn('specialization');
        });
    }
};
