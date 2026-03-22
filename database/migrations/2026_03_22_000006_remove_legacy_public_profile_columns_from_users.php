<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['has_public_profile', 'public_profile_approved_at']);
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('has_public_profile')->default(false)->after('profile_image');
            $table->timestamp('public_profile_approved_at')->nullable()->after('has_public_profile');
        });
    }
};
