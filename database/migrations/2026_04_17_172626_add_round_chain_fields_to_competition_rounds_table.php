<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('competition_rounds', function (Blueprint $table) {
            $table->foreignUuid('next_round_id')
                ->nullable()
                ->after('athlete_category_id')
                ->constrained('competition_rounds')
                ->nullOnDelete();
            $table->unsignedSmallInteger('competitor_count')->nullable()->after('advance_count');
            $table->unsignedSmallInteger('team_size')->default(1)->after('battle_size');
            $table->string('pairing_strategy')->default('random')->after('team_size');
        });
    }

    public function down(): void
    {
        Schema::table('competition_rounds', function (Blueprint $table) {
            $table->dropConstrainedForeignId('next_round_id');
            $table->dropColumn(['competitor_count', 'team_size', 'pairing_strategy']);
        });
    }
};
