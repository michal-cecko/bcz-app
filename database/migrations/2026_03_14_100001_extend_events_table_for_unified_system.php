<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->string('event_type')->default('report')->after('id')->index();
            $table->string('place_name')->nullable()->after('city');
            $table->string('place_address')->nullable()->after('place_name');
            $table->decimal('latitude', 10, 7)->nullable()->after('place_address');
            $table->decimal('longitude', 10, 7)->nullable()->after('latitude');
        });
    }

    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn(['event_type', 'place_name', 'place_address', 'latitude', 'longitude']);
        });
    }
};
