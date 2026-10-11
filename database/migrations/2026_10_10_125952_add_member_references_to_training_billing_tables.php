<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        foreach (['memberships', 'training_registrations', 'training_waitlist', 'team_join_requests'] as $name) {
            Schema::table($name, function (Blueprint $table): void {
                $table->foreignUuid('member_id')->nullable()->constrained()->restrictOnDelete();
            });
        }
        Schema::table('memberships', function (Blueprint $table): void {
            $table->dropForeign(['user_id']);
            $table->foreign('user_id')->references('id')->on('users')->restrictOnDelete();
        });
        Schema::table('training_waitlist', function (Blueprint $table): void {
            $table->dropUnique(['training_id', 'user_id']);
            $table->unique(['training_id', 'member_id']);
        });
    }

    public function down(): void
    {
        Schema::table('training_waitlist', function (Blueprint $table): void {
            $table->dropUnique(['training_id', 'member_id']);
            $table->unique(['training_id', 'user_id']);
        });
        Schema::table('memberships', function (Blueprint $table): void {
            $table->dropForeign(['user_id']);
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
        });
        foreach (['memberships', 'training_registrations', 'training_waitlist', 'team_join_requests'] as $name) {
            Schema::table($name, fn (Blueprint $table) => $table->dropConstrainedForeignId('member_id'));
        }
    }
};
