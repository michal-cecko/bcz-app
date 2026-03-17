<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('memberships', function (Blueprint $table) {
            $table->foreignUuid('team_season_id')->nullable()->after('user_id')->constrained('team_seasons')->nullOnDelete();
            $table->boolean('is_free')->default(false)->after('fee_currency');
            $table->timestamp('payment_deadline_at')->nullable()->after('is_free');
        });
    }

    public function down(): void
    {
        Schema::table('memberships', function (Blueprint $table) {
            $table->dropConstrainedForeignId('team_season_id');
            $table->dropColumn(['is_free', 'payment_deadline_at']);
        });
    }
};
