<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('trainings', function (Blueprint $table) {
            $table->json('registration_intro')->nullable()->after('registration_form_schema');
        });

        Schema::table('event_organizations', function (Blueprint $table) {
            $table->json('registration_intro')->nullable()->after('registration_form_schema');
        });
    }

    public function down(): void
    {
        Schema::table('trainings', function (Blueprint $table) {
            $table->dropColumn('registration_intro');
        });

        Schema::table('event_organizations', function (Blueprint $table) {
            $table->dropColumn('registration_intro');
        });
    }
};
