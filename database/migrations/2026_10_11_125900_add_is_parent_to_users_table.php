<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('is_parent')->default(false);
        });

        DB::table('users')->whereIn('id', DB::table('members')->select('user_id')
            ->groupBy('user_id')->havingRaw('COUNT(*) > 1'))->update(['is_parent' => true]);
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('is_parent');
        });
    }
};
