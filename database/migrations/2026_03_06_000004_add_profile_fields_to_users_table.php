<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('slug')->unique()->nullable()->after('id');
            $table->string('first_name')->nullable()->after('name');
            $table->string('last_name')->nullable()->after('first_name');
            $table->string('phone')->nullable()->after('email');
            $table->string('locale', 5)->default('sk')->after('phone');
            $table->json('socials')->nullable()->after('locale');
            $table->string('country_code', 2)->nullable()->after('socials');
            $table->string('contact_email')->nullable()->after('country_code');
            $table->string('contact_phone')->nullable()->after('contact_email');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'slug',
                'first_name',
                'last_name',
                'phone',
                'locale',
                'socials',
                'country_code',
                'contact_email',
                'contact_phone',
            ]);
        });
    }
};
