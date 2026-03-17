<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $tables = ['trainings', 'exercises', 'email_templates', 'media_items', 'inquiries'];

        foreach ($tables as $table) {
            Schema::table($table, function (Blueprint $blueprint) {
                $blueprint->foreignUuid('created_by')->nullable()->after('team_id')->constrained('users')->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        $tables = ['trainings', 'exercises', 'email_templates', 'media_items', 'inquiries'];

        foreach ($tables as $table) {
            Schema::table($table, function (Blueprint $blueprint) {
                $blueprint->dropConstrainedForeignId('created_by');
            });
        }
    }
};
