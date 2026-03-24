<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('athlete_goals', function (Blueprint $table) {
            $table->unsignedBigInteger('thumbnail_media_id')->nullable()->after('icon');
        });
    }

    public function down(): void
    {
        Schema::table('athlete_goals', function (Blueprint $table) {
            $table->dropColumn('thumbnail_media_id');
        });
    }
};
