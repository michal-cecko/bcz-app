<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('banners', function (Blueprint $table) {
            $table->dropConstrainedForeignId('page_id');
            $table->jsonb('page_ids')->nullable()->after('placement');
        });
    }

    public function down(): void
    {
        Schema::table('banners', function (Blueprint $table) {
            $table->dropColumn('page_ids');
            $table->foreignUuid('page_id')->nullable()->constrained('pages')->nullOnDelete();
        });
    }
};
