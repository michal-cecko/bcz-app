<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('competition_rounds', function (Blueprint $table) {
            $table->string('scoring_format')->nullable()->after('name');
        });

        Schema::table('disciplines', function (Blueprint $table) {
            $table->dropColumn(['scoring_format', 'scoring_description']);
        });
    }

    public function down(): void
    {
        Schema::table('disciplines', function (Blueprint $table) {
            $table->string('scoring_format')->default('points');
            $table->json('scoring_description')->nullable();
        });

        Schema::table('competition_rounds', function (Blueprint $table) {
            $table->dropColumn('scoring_format');
        });
    }
};
