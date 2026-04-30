<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('training_registrations', function (Blueprint $table): void {
            $table->string('locale', 5)->default('sk')->after('status');
        });

        Schema::table('event_registrations', function (Blueprint $table): void {
            $table->string('locale', 5)->default('sk')->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('training_registrations', function (Blueprint $table): void {
            $table->dropColumn('locale');
        });

        Schema::table('event_registrations', function (Blueprint $table): void {
            $table->dropColumn('locale');
        });
    }
};
