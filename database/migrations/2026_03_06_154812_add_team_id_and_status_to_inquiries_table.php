<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('inquiries', function (Blueprint $table) {
            $table->foreignUuid('team_id')->nullable()->after('id')->constrained()->nullOnDelete();
            $table->string('status')->default('new')->after('reason');
            $table->dropColumn('is_read');
        });
    }

    public function down(): void
    {
        Schema::table('inquiries', function (Blueprint $table) {
            $table->boolean('is_read')->default(false)->after('reason');
            $table->dropColumn('status');
            $table->dropConstrainedForeignId('team_id');
        });
    }
};
