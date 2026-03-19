<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('training_registrations', function (Blueprint $table) {
            $table->timestamp('payment_due_at')->nullable()->after('registered_at');
        });
    }

    public function down(): void
    {
        Schema::table('training_registrations', function (Blueprint $table) {
            $table->dropColumn('payment_due_at');
        });
    }
};
