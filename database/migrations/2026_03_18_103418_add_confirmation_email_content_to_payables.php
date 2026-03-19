<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('trainings', function (Blueprint $table) {
            $table->jsonb('confirmation_email_content')->nullable();
        });

        Schema::table('event_organizations', function (Blueprint $table) {
            $table->jsonb('confirmation_email_content')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('trainings', function (Blueprint $table) {
            $table->dropColumn('confirmation_email_content');
        });

        Schema::table('event_organizations', function (Blueprint $table) {
            $table->dropColumn('confirmation_email_content');
        });
    }
};
