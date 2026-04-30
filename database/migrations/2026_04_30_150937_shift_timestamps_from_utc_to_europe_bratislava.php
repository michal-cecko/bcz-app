<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Shift Laravel system-managed timestamp columns from UTC to Europe/Bratislava.
 *
 * Context: app.timezone changed from UTC to Europe/Bratislava. Existing values
 * in `created_at`, `updated_at`, `deleted_at` were written by `now()` (UTC).
 * Carbon now reads them as Europe/Bratislava, making them appear ~1–2h earlier
 * than reality (DST-dependent). This migration reinterprets each value as UTC
 * and converts to Europe/Bratislava local — Postgres handles DST per record.
 *
 * NOT shifted (intentionally): user-entered DateTimePicker columns such as
 *   - events.published_at, pages.published_at, competitions.published_at,
 *     competition_reports.published_at
 *   - event_organizations.registration_opens_at / registration_closes_at
 *   - trainings.registration_opens_at / registration_closes_at
 *   - competitions.registration_opens_at / registration_closes_at
 *   - timetable_entries.scheduled_time / actual_start_time / actual_end_time
 *   - {event,training,competition}_registrations.registered_at
 *   - payments.paid_at, team_payouts.paid_at
 *   - banners.active_from / active_to
 *
 * These were stored verbatim from the picker (which previously treated input
 * as UTC). After the timezone switch, the same literal is correctly interpreted
 * as Europe/Bratislava local time — no shift required.
 */
return new class extends Migration
{
    /** @var list<string> */
    private array $shiftableColumns = ['created_at', 'updated_at', 'deleted_at'];

    /** @var list<string> Tables whose timestamps are not human/local datetimes. */
    private array $skipTables = [
        'migrations',
        'cache',
        'cache_locks',
        'sessions',
        'jobs',
        'job_batches',
        'failed_jobs',
        'password_reset_tokens',
        'telescope_entries',
        'telescope_entries_tags',
        'telescope_monitoring',
    ];

    public function up(): void
    {
        $this->shift('UTC', 'Europe/Bratislava');
    }

    public function down(): void
    {
        $this->shift('Europe/Bratislava', 'UTC');
    }

    private function shift(string $fromTz, string $toTz): void
    {
        if (DB::connection()->getDriverName() !== 'pgsql') {
            return;
        }

        foreach ($this->getTablesWithTimestamps() as $table => $columns) {
            foreach ($columns as $column) {
                DB::statement(sprintf(
                    'UPDATE "%s" SET "%s" = ("%s" AT TIME ZONE %s) AT TIME ZONE %s WHERE "%s" IS NOT NULL',
                    $table,
                    $column,
                    $column,
                    DB::getPdo()->quote($fromTz),
                    DB::getPdo()->quote($toTz),
                    $column,
                ));
            }
        }
    }

    /**
     * @return array<string, list<string>>
     */
    private function getTablesWithTimestamps(): array
    {
        $rows = DB::select(<<<'SQL'
            SELECT table_name, column_name
            FROM information_schema.columns
            WHERE table_schema = current_schema()
              AND column_name IN ('created_at', 'updated_at', 'deleted_at')
              AND data_type IN ('timestamp without time zone', 'timestamp with time zone')
            ORDER BY table_name, column_name
        SQL);

        $result = [];
        foreach ($rows as $row) {
            if (in_array($row->table_name, $this->skipTables, true)) {
                continue;
            }
            $result[$row->table_name][] = $row->column_name;
        }

        return $result;
    }
};
