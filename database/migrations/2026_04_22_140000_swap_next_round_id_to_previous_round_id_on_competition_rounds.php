<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('competition_rounds', function (Blueprint $table) {
            $table->foreignUuid('previous_round_id')
                ->nullable()
                ->after('athlete_category_id')
                ->constrained('competition_rounds')
                ->nullOnDelete();
        });

        foreach (DB::table('competition_rounds')->whereNotNull('next_round_id')->get(['id', 'next_round_id']) as $row) {
            DB::table('competition_rounds')
                ->where('id', $row->next_round_id)
                ->update(['previous_round_id' => $row->id]);
        }

        Schema::table('competition_rounds', function (Blueprint $table) {
            $table->dropConstrainedForeignId('next_round_id');
        });
    }

    public function down(): void
    {
        Schema::table('competition_rounds', function (Blueprint $table) {
            $table->foreignUuid('next_round_id')
                ->nullable()
                ->after('athlete_category_id')
                ->constrained('competition_rounds')
                ->nullOnDelete();
        });

        foreach (DB::table('competition_rounds')->whereNotNull('previous_round_id')->get(['id', 'previous_round_id']) as $row) {
            DB::table('competition_rounds')
                ->where('id', $row->previous_round_id)
                ->update(['next_round_id' => $row->id]);
        }

        Schema::table('competition_rounds', function (Blueprint $table) {
            $table->dropConstrainedForeignId('previous_round_id');
        });
    }
};
