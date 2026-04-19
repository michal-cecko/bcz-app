<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('timetable_entries', function (Blueprint $table) {
            $table->json('description')->nullable()->after('title');
            $table->string('type')->nullable()->after('description');
            $table->foreignUuid('competition_round_id')->nullable()->after('type')->constrained()->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('timetable_entries', function (Blueprint $table) {
            $table->dropConstrainedForeignId('competition_round_id');
            $table->dropColumn(['description', 'type']);
        });
    }
};
