<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('team_seasons', function (Blueprint $table) {
            $table->boolean('prorate_fee')->default(false)->after('fee_currency');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('team_seasons', function (Blueprint $table) {
            $table->dropColumn('prorate_fee');
        });
    }
};
