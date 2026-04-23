<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('team_seasons', function (Blueprint $table) {
            $table->dropColumn('variable_symbol');
        });

        Schema::table('trainings', function (Blueprint $table) {
            $table->dropColumn('variable_symbol');
        });

        Schema::table('event_organizations', function (Blueprint $table) {
            $table->dropColumn('variable_symbol');
        });
    }

    public function down(): void
    {
        Schema::table('team_seasons', function (Blueprint $table) {
            $table->string('variable_symbol')->nullable()->after('fee_currency');
        });

        Schema::table('trainings', function (Blueprint $table) {
            $table->string('variable_symbol')->nullable()->after('price_amount');
        });

        Schema::table('event_organizations', function (Blueprint $table) {
            $table->string('variable_symbol')->nullable()->after('price_currency');
        });
    }
};
