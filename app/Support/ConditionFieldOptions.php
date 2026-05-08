<?php

namespace App\Support;

use App\Enums\GenderEnum;
use App\Enums\RegistrationFieldTypeEnum;
use App\Models\Event;
use App\Models\Training;
use Filament\Forms\Components\Repeater;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Utilities\Get;

class ConditionFieldOptions
{
    /**
     * Find a sibling field's schema (in the same registration_form_schema Repeater)
     * by its `name`. Used by the conditional-visibility "Očakávané hodnoty" input
     * to discover the source field's type/options at form-render time.
     *
     * @return array<string, mixed>|null
     */
    public static function findSourceField(Get $get, ?string $sourceFieldName): ?array
    {
        if ($sourceFieldName === null || $sourceFieldName === '') {
            return null;
        }

        // From within the condition Section (sibling of has_condition / condition_field):
        // ../  → field item, ../../ → repeater items array.
        $items = $get('../../');
        if (! is_array($items)) {
            return null;
        }

        foreach ($items as $item) {
            if (! is_array($item)) {
                continue;
            }
            if (($item['name'] ?? null) === $sourceFieldName) {
                return $item;
            }
        }

        return null;
    }

    /**
     * @param  array<string, mixed>|null  $sourceField
     */
    public static function isOptionBased(?array $sourceField): bool
    {
        return in_array($sourceField['type'] ?? null, [
            RegistrationFieldTypeEnum::SELECT->value,
            RegistrationFieldTypeEnum::MULTI_SELECT->value,
            RegistrationFieldTypeEnum::CATEGORY->value,
            RegistrationFieldTypeEnum::GENDER->value,
        ], true);
    }

    /**
     * Build the [value => label] options for the condition_values Select based on
     * the source field's type:
     *  - select/multi_select → the field's own options array
     *  - gender              → male / female from GenderEnum
     *  - category            → athlete categories from the Event (when available)
     *  - anything else       → empty (caller should fall back to TagsInput)
     *
     * @param  array<string, mixed>|null  $sourceField
     * @return array<string, string>
     */
    public static function valueOptionsForSource(?array $sourceField, string $locale, Event|Training|null $owner = null): array
    {
        if ($sourceField === null) {
            return [];
        }

        $type = $sourceField['type'] ?? null;

        if ($type === RegistrationFieldTypeEnum::GENDER->value) {
            return [
                GenderEnum::MALE->value => GenderEnum::MALE->getLabel(),
                GenderEnum::FEMALE->value => GenderEnum::FEMALE->getLabel(),
            ];
        }

        if (in_array($type, [
            RegistrationFieldTypeEnum::SELECT->value,
            RegistrationFieldTypeEnum::MULTI_SELECT->value,
            RegistrationFieldTypeEnum::CATEGORY->value,
        ], true)) {
            return RegistrationFieldOptions::resolve($sourceField, $locale, $owner);
        }

        return [];
    }

    /**
     * Build the option list for the conditional-visibility "Pole" Select inside
     * a registration_form_schema Repeater item. Walks up the schema tree to find
     * the parent Repeater, reads its current raw state (including unsaved items
     * not yet submitted), and returns [name => label] for every sibling field
     * that has a key. The current item is excluded so a field can't depend on
     * itself.
     *
     * @return array<string, string>
     */
    public static function forCurrent(Component $component): array
    {
        $repeater = self::findParentRepeater($component);

        if ($repeater === null) {
            return [];
        }

        $items = $repeater->getRawState();
        if (! is_array($items)) {
            return [];
        }

        $currentItemKey = self::currentItemKey($component, $repeater);

        $options = [];
        foreach ($items as $key => $item) {
            if (! is_array($item)) {
                continue;
            }
            if ($key === $currentItemKey) {
                continue;
            }
            $name = $item['name'] ?? null;
            if (! is_string($name) || $name === '') {
                continue;
            }
            $options[$name] = self::resolveLabel($item, $name);
        }

        return $options;
    }

    private static function findParentRepeater(Component $component): ?Repeater
    {
        $cursor = $component->getContainer()?->getParentComponent();

        while ($cursor !== null) {
            if ($cursor instanceof Repeater) {
                return $cursor;
            }
            $cursor = $cursor->getContainer()?->getParentComponent();
        }

        return null;
    }

    private static function currentItemKey(Component $component, Repeater $repeater): ?string
    {
        // The current Repeater item key sits between the Repeater's state path
        // and the inner field's state path: e.g. "...registration_form_schema.{uuid}.condition_field".
        $repeaterPath = $repeater->getStatePath();
        $componentPath = $component->getStatePath();

        if ($repeaterPath === '' || ! str_starts_with($componentPath, $repeaterPath.'.')) {
            return null;
        }

        $relative = substr($componentPath, strlen($repeaterPath) + 1);
        $key = strtok($relative, '.');

        return $key === false ? null : $key;
    }

    /**
     * @param  array<string, mixed>  $item
     */
    private static function resolveLabel(array $item, string $fallback): string
    {
        $label = $item['label'] ?? null;

        if (is_array($label)) {
            $sk = $label['sk'] ?? null;
            if (is_string($sk) && $sk !== '') {
                return $sk;
            }
            $first = collect($label)->filter(fn ($v) => is_string($v) && $v !== '')->first();
            if (is_string($first) && $first !== '') {
                return $first;
            }
        } elseif (is_string($label) && $label !== '') {
            return $label;
        }

        return $fallback;
    }
}
