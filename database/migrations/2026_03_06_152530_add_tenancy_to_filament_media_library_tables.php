<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (! Schema::hasColumns('filament_media_library', ['tenant_type', 'tenant_id'])) {
            Schema::table('filament_media_library', function (Blueprint $table) {
                $table->after('id', function (Blueprint $table) {
                    $table->nullableUuidMorphs('tenant');
                });
            });
        }

        if (! Schema::hasColumns('filament_media_library_folders', ['tenant_type', 'tenant_id'])) {
            Schema::table('filament_media_library_folders', function (Blueprint $table) {
                $table->after('id', function (Blueprint $table) {
                    $table->nullableUuidMorphs('tenant');
                });
            });
        }
    }

    public function down()
    {
        Schema::table('filament_media_library', function (Blueprint $table) {
            $table->dropMorphs('tenant');
        });

        Schema::table('filament_media_library_folders', function (Blueprint $table) {
            $table->dropMorphs('tenant');
        });
    }
};
