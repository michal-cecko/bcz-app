<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('memberships', function (Blueprint $table): void {
            $table->timestamp('payment_reminder_sent_at')->nullable()->after('payment_deadline_at');
        });

        Schema::table('training_registrations', function (Blueprint $table): void {
            $table->timestamp('payment_reminder_sent_at')->nullable()->after('payment_due_at');
        });

        Schema::table('event_registrations', function (Blueprint $table): void {
            $table->timestamp('payment_due_at')->nullable()->after('registered_at');
            $table->timestamp('payment_reminder_sent_at')->nullable()->after('payment_due_at');
        });
    }

    public function down(): void
    {
        Schema::table('memberships', function (Blueprint $table): void {
            $table->dropColumn('payment_reminder_sent_at');
        });

        Schema::table('training_registrations', function (Blueprint $table): void {
            $table->dropColumn('payment_reminder_sent_at');
        });

        Schema::table('event_registrations', function (Blueprint $table): void {
            $table->dropColumn(['payment_reminder_sent_at', 'payment_due_at']);
        });
    }
};
