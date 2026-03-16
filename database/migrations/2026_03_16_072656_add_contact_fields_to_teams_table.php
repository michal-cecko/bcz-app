<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('teams', function (Blueprint $table) {
            $table->string('contact_email')->nullable()->after('default_locale');
            $table->string('contact_phone')->nullable()->after('contact_email');
            $table->string('contact_website')->nullable()->after('contact_phone');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('teams', function (Blueprint $table) {
            $table->dropColumn(['contact_email', 'contact_phone', 'contact_website']);
        });
    }
};
