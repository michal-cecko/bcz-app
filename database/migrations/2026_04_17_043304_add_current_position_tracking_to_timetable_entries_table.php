<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('timetable_entries', function (Blueprint $table) {
            $table->unsignedInteger('current_competitor_index')->nullable()->after('status');
            $table->foreignUuid('current_battle_id')->nullable()->after('current_competitor_index')
                ->constrained('battles')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('timetable_entries', function (Blueprint $table) {
            $table->dropConstrainedForeignId('current_battle_id');
            $table->dropColumn('current_competitor_index');
        });
    }
};
