<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('battles', function (Blueprint $table) {
            $table->dropColumn(['score_a', 'score_b']);
        });
    }

    public function down(): void
    {
        Schema::table('battles', function (Blueprint $table) {
            $table->decimal('score_a', 8, 2)->nullable();
            $table->decimal('score_b', 8, 2)->nullable();
        });
    }
};
