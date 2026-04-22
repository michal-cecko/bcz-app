<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->bigInteger('sequence_number')->nullable()->unique()->after('id');
        });

        $driver = DB::connection()->getDriverName();

        if ($driver === 'pgsql') {
            DB::statement('CREATE SEQUENCE IF NOT EXISTS payments_sequence_number_seq');
            DB::statement("ALTER TABLE payments ALTER COLUMN sequence_number SET DEFAULT nextval('payments_sequence_number_seq')");
            DB::statement('ALTER SEQUENCE payments_sequence_number_seq OWNED BY payments.sequence_number');

            DB::statement('
                UPDATE payments SET sequence_number = sub.rn
                FROM (SELECT id, ROW_NUMBER() OVER (ORDER BY created_at, id) AS rn FROM payments WHERE sequence_number IS NULL) sub
                WHERE payments.id = sub.id
            ');

            DB::statement("SELECT setval('payments_sequence_number_seq', COALESCE((SELECT MAX(sequence_number) FROM payments), 0) + 1, false)");
        } else {
            // SQLite + others: assign sequentially at app layer (Model creating event).
            // Backfill existing rows ordered by created_at.
            $rows = DB::table('payments')
                ->whereNull('sequence_number')
                ->orderBy('created_at')
                ->orderBy('id')
                ->get(['id']);

            foreach ($rows as $i => $row) {
                DB::table('payments')
                    ->where('id', $row->id)
                    ->update(['sequence_number' => $i + 1]);
            }
        }
    }

    public function down(): void
    {
        if (DB::connection()->getDriverName() === 'pgsql') {
            DB::statement('ALTER TABLE payments ALTER COLUMN sequence_number DROP DEFAULT');
            DB::statement('DROP SEQUENCE IF EXISTS payments_sequence_number_seq');
        }

        Schema::table('payments', function (Blueprint $table) {
            $table->dropUnique(['sequence_number']);
            $table->dropColumn('sequence_number');
        });
    }
};
