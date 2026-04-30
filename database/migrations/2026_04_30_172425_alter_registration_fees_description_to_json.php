<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Stash existing single-string descriptions, then convert column to json,
        // then reseed the values as the SK locale entry.
        $rows = DB::table('registration_fees')
            ->select('id', 'description')
            ->whereNotNull('description')
            ->get();

        Schema::table('registration_fees', function (Blueprint $table): void {
            $table->dropColumn('description');
        });

        Schema::table('registration_fees', function (Blueprint $table): void {
            $table->json('description')->nullable();
        });

        foreach ($rows as $row) {
            DB::table('registration_fees')
                ->where('id', $row->id)
                ->update(['description' => json_encode(['sk' => $row->description])]);
        }
    }

    public function down(): void
    {
        $rows = DB::table('registration_fees')
            ->select('id', 'description')
            ->whereNotNull('description')
            ->get();

        Schema::table('registration_fees', function (Blueprint $table): void {
            $table->dropColumn('description');
        });

        Schema::table('registration_fees', function (Blueprint $table): void {
            $table->string('description')->nullable();
        });

        foreach ($rows as $row) {
            $decoded = json_decode((string) $row->description, true);
            $value = is_array($decoded) ? ($decoded['sk'] ?? reset($decoded) ?: null) : $row->description;

            DB::table('registration_fees')
                ->where('id', $row->id)
                ->update(['description' => $value]);
        }
    }
};
