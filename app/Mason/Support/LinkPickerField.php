<?php

namespace App\Mason\Support;

use App\Enums\LinkTypeEnum;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Utilities\Get;

class LinkPickerField
{
    /**
     * Returns a Fieldset with all link picker fields.
     *
     * When $locale is provided, the custom URL and text fields get locale suffixes.
     * The type and model_id fields are locale-independent (same value across all locales).
     *
     * @param  string|null  $textFieldName  State path for the link text field (e.g. "cta_text"). Locale suffix is added automatically.
     * @param  string|null  $textFieldLabel  Label for the text field.
     */
    public static function make(
        string $prefix = '',
        ?string $locale = null,
        ?string $label = null,
        ?string $textFieldName = null,
        ?string $textFieldLabel = null,
    ): Fieldset {
        $urlName = $locale ? "{$prefix}link_url.{$locale}" : "{$prefix}link_url";

        $fields = [];

        if ($textFieldName) {
            $textName = $locale ? "{$textFieldName}.{$locale}" : $textFieldName;
            $fields[] = TextInput::make($textName)
                ->label($textFieldLabel ?? __('bricks.link_picker.text'));
        }

        $fields[] = Select::make("{$prefix}link_type")
            ->label(__('bricks.link_picker.type'))
            ->options(LinkTypeEnum::options())
            ->live()
            ->afterStateUpdated(fn (callable $set) => $set("{$prefix}link_model_id", null));

        $fields[] = Select::make("{$prefix}link_model_id")
            ->label(__('bricks.link_picker.record'))
            ->searchable()
            ->options(function (Get $get) use ($prefix): array {
                $type = LinkTypeEnum::tryFrom($get("{$prefix}link_type") ?? '');

                if (! $type || $type === LinkTypeEnum::Custom) {
                    return [];
                }

                $modelClass = $type->getModelClass();

                if (! $modelClass) {
                    return [];
                }

                $roleFilter = $type->getRoleFilter();

                if ($roleFilter) {
                    return $modelClass::linkableOptionsForRole($roleFilter)->all();
                }

                return $modelClass::linkableOptions()->all();
            })
            ->visible(function (Get $get) use ($prefix): bool {
                $type = LinkTypeEnum::tryFrom($get("{$prefix}link_type") ?? '');

                return $type !== null && $type !== LinkTypeEnum::Custom;
            });

        $fields[] = TextInput::make($urlName)
            ->label(__('bricks.link_picker.url'))
            ->visible(function (Get $get) use ($prefix): bool {
                $type = LinkTypeEnum::tryFrom($get("{$prefix}link_type") ?? '');

                return $type === LinkTypeEnum::Custom;
            });

        return Fieldset::make($label ?? __('bricks.link_picker.fieldset'))
            ->schema($fields)
            ->columns(1);
    }
}
