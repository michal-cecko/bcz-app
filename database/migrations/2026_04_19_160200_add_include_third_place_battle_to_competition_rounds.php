<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('competition_rounds', function (Blueprint $table) {
            $table->boolean('include_third_place_battle')->default(false)->after('pairing_strategy');
        });
    }

    public function down(): void
    {
        Schema::table('competition_rounds', function (Blueprint $table) {
            $table->dropColumn('include_third_place_battle');
        });
    }
};
