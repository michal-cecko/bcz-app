<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('trainings', function (Blueprint $table) {
            $table->unsignedSmallInteger('min_age')->nullable()->after('age_group');
            $table->unsignedSmallInteger('max_age')->nullable()->after('min_age');
        });

        // Migrate existing age_group data
        DB::table('trainings')->whereNotNull('age_group')->orderBy('id')->each(function ($training) {
            $ageGroup = trim($training->age_group);
            $minAge = null;
            $maxAge = null;

            if (str_ends_with($ageGroup, '+')) {
                $minAge = (int) rtrim($ageGroup, '+');
            } elseif (str_contains($ageGroup, '-')) {
                [$minAge, $maxAge] = array_map('intval', explode('-', $ageGroup, 2));
            }

            DB::table('trainings')->where('id', $training->id)->update([
                'min_age' => $minAge,
                'max_age' => $maxAge,
            ]);
        });

        Schema::table('trainings', function (Blueprint $table) {
            $table->dropColumn('age_group');
        });
    }

    public function down(): void
    {
        Schema::table('trainings', function (Blueprint $table) {
            $table->string('age_group')->nullable()->after('description');
        });

        DB::table('trainings')->each(function ($training) {
            $ageGroup = null;

            if ($training->min_age !== null && $training->max_age !== null) {
                $ageGroup = "{$training->min_age}-{$training->max_age}";
            } elseif ($training->min_age !== null) {
                $ageGroup = "{$training->min_age}+";
            }

            DB::table('trainings')->where('id', $training->id)->update([
                'age_group' => $ageGroup,
            ]);
        });

        Schema::table('trainings', function (Blueprint $table) {
            $table->dropColumn(['min_age', 'max_age']);
        });
    }
};
