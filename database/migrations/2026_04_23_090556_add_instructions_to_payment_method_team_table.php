<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payment_method_team', function (Blueprint $table) {
            $table->json('title')->nullable()->after('team_id');
            $table->json('description')->nullable()->after('title');
            $table->json('instructions')->nullable()->after('description');
        });
    }

    public function down(): void
    {
        Schema::table('payment_method_team', function (Blueprint $table) {
            $table->dropColumn(['title', 'description', 'instructions']);
        });
    }
};
