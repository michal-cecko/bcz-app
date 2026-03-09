<?php

namespace App\Mason\Support;

use Filament\Forms\Components\Field;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Support\Icons\Heroicon;

class TranslatableBrickFields
{
    /** @var list<string> */
    private const array LOCALES = ['sk', 'en', 'cz'];

    /**
     * Group multiple translatable fields into locale tabs.
     *
     * The callback receives a locale code and should return an array of form fields
     * with names suffixed with .{locale} (e.g. TextInput::make("title.{$locale}")).
     *
     * Shows a warning badge on tabs where all fields are empty.
     *
     * @param  callable(string): array  $fieldBuilder
     */
    public static function group(callable $fieldBuilder): Tabs
    {
        return Tabs::make('translations')
            ->tabs(
                array_map(
                    function (string $locale) use ($fieldBuilder) {
                        $fields = $fieldBuilder($locale);
                        $fieldNames = self::extractFieldNames($fields);

                        return Tabs\Tab::make(strtoupper($locale))
                            ->schema($fields)
                            ->badge(function (Get $get) use ($fieldNames): ?string {
                                if ($fieldNames === []) {
                                    return null;
                                }

                                foreach ($fieldNames as $name) {
                                    if (! empty($get($name))) {
                                        return null;
                                    }
                                }

                                return 'Chýba';
                            })
                            ->badgeColor('warning')
                            ->badgeIcon(Heroicon::ExclamationTriangle)
                            ->badgeTooltip('Chýba preklad');
                    },
                    self::LOCALES
                )
            )
            ->columnSpanFull();
    }

    /**
     * Extract field names from a flat array of form components.
     *
     * @return list<string>
     */
    private static function extractFieldNames(array $fields): array
    {
        $names = [];

        foreach ($fields as $field) {
            if ($field instanceof Field) {
                $names[] = $field->getName();
            }
        }

        return $names;
    }
}
