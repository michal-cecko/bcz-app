<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('athlete_exercises', function (Blueprint $table) {
            $table->string('custom_name')->nullable()->after('exercise_id');
        });

        Schema::table('athlete_exercises', function (Blueprint $table) {
            $table->foreignUuid('exercise_id')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('athlete_exercises', function (Blueprint $table) {
            $table->dropColumn('custom_name');
        });

        Schema::table('athlete_exercises', function (Blueprint $table) {
            $table->foreignUuid('exercise_id')->nullable(false)->change();
        });
    }
};
