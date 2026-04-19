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
        Schema::table('competition_rounds', function (Blueprint $table) {
            $table->json('competitor_order')->nullable()->after('scores_published');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('competition_rounds', function (Blueprint $table) {
            $table->dropColumn('competitor_order');
        });
    }
};
