<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('team_seasons', function (Blueprint $table) {
            $table->timestamp('renewal_notified_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('team_seasons', function (Blueprint $table) {
            $table->dropColumn('renewal_notified_at');
        });
    }
};
