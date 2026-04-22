<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::connection()->getDriverName() !== 'pgsql') {
            // SQLite and other drivers have flexible column types; Spatie Translatable stores
            // JSON at the application layer, so no schema change is needed for tests.
            return;
        }

        DB::statement("ALTER TABLE payment_methods ALTER COLUMN title TYPE jsonb USING jsonb_build_object('sk', title)");
        DB::statement("ALTER TABLE payment_methods ALTER COLUMN description TYPE jsonb USING CASE WHEN description IS NULL THEN NULL ELSE jsonb_build_object('sk', description) END");
    }

    public function down(): void
    {
        if (DB::connection()->getDriverName() !== 'pgsql') {
            return;
        }

        DB::statement("ALTER TABLE payment_methods ALTER COLUMN title TYPE varchar(255) USING COALESCE(title->>'sk', '')");
        DB::statement("ALTER TABLE payment_methods ALTER COLUMN description TYPE text USING description->>'sk'");
    }
};
