<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('coach_profiles', function (Blueprint $table) {
            $table->json('draft_data')->nullable()->after('biography_image');
            $table->string('draft_status')->nullable()->after('draft_data');
            $table->text('draft_rejection_reason')->nullable()->after('draft_status');
            $table->timestamp('draft_submitted_at')->nullable()->after('draft_rejection_reason');
        });
    }

    public function down(): void
    {
        Schema::table('coach_profiles', function (Blueprint $table) {
            $table->dropColumn(['draft_data', 'draft_status', 'draft_rejection_reason', 'draft_submitted_at']);
        });
    }
};
