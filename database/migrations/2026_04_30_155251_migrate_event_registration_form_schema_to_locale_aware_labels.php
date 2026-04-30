<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('event_organizations')
            ->whereNotNull('registration_form_schema')
            ->orderBy('id')
            ->each(function (object $row): void {
                $schema = json_decode($row->registration_form_schema, true);
                if (! is_array($schema)) {
                    return;
                }

                $migrated = array_map(
                    static fn (array $field): array => self::migrateField($field),
                    $schema
                );

                DB::table('event_organizations')
                    ->where('id', $row->id)
                    ->update(['registration_form_schema' => json_encode($migrated)]);
            });
    }

    public function down(): void
    {
        // Irreversible: a down would have to discard EN/CS translations.
    }

    /**
     * @param  array<string, mixed>  $field
     * @return array<string, mixed>
     */
    private static function migrateField(array $field): array
    {
        if (isset($field['key']) && ! isset($field['name'])) {
            $field['name'] = $field['key'];
        }
        unset($field['key']);

        if (isset($field['label']) && is_string($field['label'])) {
            $field['label'] = [
                'sk' => $field['label'],
                'en' => '',
                'cs' => '',
            ];
        }

        if (isset($field['placeholder']) && is_string($field['placeholder'])) {
            $field['placeholder'] = [
                'sk' => $field['placeholder'],
                'en' => '',
                'cs' => '',
            ];
        }

        if (! array_key_exists('width', $field)) {
            $field['width'] = 'half';
        }

        if (! array_key_exists('has_condition', $field)) {
            $field['has_condition'] = false;
        }

        return $field;
    }
};
