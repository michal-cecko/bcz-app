<?php

namespace App\Support;

use App\Models\Event;
use App\Models\Training;

class RegistrationFieldOptions
{
    /**
     * Resolve a registration field's options as [value => label] for the given locale.
     *
     * Handles three storage shapes:
     *  - new: array of {value, label: {sk,en,cs}}
     *  - legacy: array of strings
     *  - legacy: comma- or newline-separated string
     *
     * For type=category, options are derived from the event's competition athlete categories.
     *
     * @param  array<string, mixed>  $field
     * @return array<string, string>
     */
    public static function resolve(array $field, string $locale, Event|Training|null $owner = null): array
    {
        $type = $field['type'] ?? null;

        if ($type === 'category') {
            return self::categoryOptions($owner, $locale);
        }

        $raw = $field['options'] ?? null;

        if (empty($raw) && $raw !== '0') {
            return [];
        }

        if (is_string($raw)) {
            $items = preg_split('/,|\r\n|\r|\n/', $raw) ?: [];
            $items = array_values(array_filter(array_map('trim', $items), fn ($v) => $v !== ''));

            return array_combine($items, $items);
        }

        if (! is_array($raw)) {
            return [];
        }

        $result = [];
        foreach ($raw as $opt) {
            if (is_array($opt) && array_key_exists('value', $opt)) {
                $value = (string) $opt['value'];
                $label = is_array($opt['label'] ?? null)
                    ? ($opt['label'][$locale] ?? $opt['label']['sk'] ?? $value)
                    : ((string) ($opt['label'] ?? $value));
                $result[$value] = (string) $label;

                continue;
            }

            if (is_scalar($opt)) {
                $value = (string) $opt;
                $result[$value] = $value;
            }
        }

        return $result;
    }

    /**
     * Look up a single value's label using the field's options map.
     */
    public static function labelFor(array $field, mixed $value, string $locale, Event|Training|null $owner = null): string
    {
        if ($value === null || $value === '') {
            return '';
        }

        $options = self::resolve($field, $locale, $owner);

        if (is_array($value)) {
            $labels = [];
            foreach ($value as $v) {
                $key = (string) $v;
                $labels[] = $options[$key] ?? $key;
            }

            return implode(', ', $labels);
        }

        $key = (string) $value;

        return $options[$key] ?? $key;
    }

    /**
     * Default required fields with locale-aware labels (Training schema shape:
     * `name`, `label.{sk,en,cs}`, `placeholder.{sk,en,cs}`, `width`, `type`,
     * `required`, `has_condition`).
     *
     * @return list<array<string, mixed>>
     */
    public static function defaultRequiredFields(): array
    {
        return [
            [
                'name' => 'meno',
                'type' => 'first_name',
                'label' => ['sk' => 'Meno', 'en' => 'First name', 'cs' => 'Jméno'],
                'placeholder' => ['sk' => '', 'en' => '', 'cs' => ''],
                'width' => 'half',
                'required' => true,
                'has_condition' => false,
            ],
            [
                'name' => 'priezvisko',
                'type' => 'last_name',
                'label' => ['sk' => 'Priezvisko', 'en' => 'Last name', 'cs' => 'Příjmení'],
                'placeholder' => ['sk' => '', 'en' => '', 'cs' => ''],
                'width' => 'half',
                'required' => true,
                'has_condition' => false,
            ],
            [
                'name' => 'email',
                'type' => 'email',
                'label' => ['sk' => 'Email', 'en' => 'Email', 'cs' => 'Email'],
                'placeholder' => ['sk' => 'tvoj@email.sk', 'en' => 'your@email.com', 'cs' => 'tvuj@email.cz'],
                'width' => 'half',
                'required' => true,
                'has_condition' => false,
            ],
            [
                'name' => 'telefon',
                'type' => 'phone',
                'label' => ['sk' => 'Telefón', 'en' => 'Phone', 'cs' => 'Telefon'],
                'placeholder' => ['sk' => '+421 XXX XXX XXX', 'en' => '+421 XXX XXX XXX', 'cs' => '+420 XXX XXX XXX'],
                'width' => 'half',
                'required' => true,
                'has_condition' => false,
            ],
        ];
    }

    /**
     * @return array<string, string>
     */
    private static function categoryOptions(Event|Training|null $owner, string $locale): array
    {
        if (! $owner instanceof Event) {
            return [];
        }

        $competition = $owner->competitionDetail;
        if (! $competition) {
            return [];
        }

        $categories = $competition->athleteCategories()
            ->orderBy('sort_order')
            ->get();

        $result = [];
        foreach ($categories as $category) {
            $label = $category->getTranslation('name', $locale)
                ?: $category->getTranslation('name', 'sk')
                ?: (string) $category->id;
            $result[(string) $category->id] = (string) $label;
        }

        return $result;
    }
}
