<?php

namespace App\Support;

use Filament\Forms\Components\Repeater;
use Filament\Schemas\Components\Component;

class ConditionFieldOptions
{
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
