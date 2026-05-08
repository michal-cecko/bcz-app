<?php

namespace App\Support;

use App\Enums\GenderEnum;
use App\Enums\RegistrationFieldTypeEnum;
use App\Models\Event;
use App\Models\Training;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Throwable;

/**
 * Resolves a single submitted registration field for human-readable display
 * in the admin panel. Maps raw stored values (option keys, UUIDs, file paths,
 * ISO dates) onto the labels admins actually want to read.
 */
class RegistrationFieldFormatter
{
    /**
     * @param  array<string, mixed>  $field  Field definition from registration_form_schema.
     * @return array{label: string, type: ?string, value: string, fileUrl: ?string, fileName: ?string, isImage: bool, isFile: bool}
     */
    public static function format(
        array $field,
        mixed $value,
        string $locale,
        Event|Training|null $owner = null,
    ): array {
        $type = $field['type'] ?? null;
        $label = self::resolveLabel($field, $locale);

        $fileUrl = null;
        $fileName = null;
        $isImage = false;
        $isFile = false;

        if ($value === null || $value === '') {
            return [
                'label' => $label,
                'type' => $type,
                'value' => '—',
                'fileUrl' => null,
                'fileName' => null,
                'isImage' => false,
                'isFile' => false,
            ];
        }

        $display = match (true) {
            in_array($type, [
                RegistrationFieldTypeEnum::SELECT->value,
                RegistrationFieldTypeEnum::MULTI_SELECT->value,
                RegistrationFieldTypeEnum::CATEGORY->value,
            ], true) => RegistrationFieldOptions::labelFor($field, $value, $locale, $owner),

            $type === RegistrationFieldTypeEnum::GENDER->value => self::genderLabel($value),

            in_array($type, [
                RegistrationFieldTypeEnum::DATE_PICKER->value,
                RegistrationFieldTypeEnum::BIRTH_DATE->value,
            ], true) => self::formatDate($value),

            $type === RegistrationFieldTypeEnum::FILE_INPUT->value && is_string($value) => null,

            default => is_array($value) ? implode(', ', array_map('strval', $value)) : (string) $value,
        };

        if ($type === RegistrationFieldTypeEnum::FILE_INPUT->value && is_string($value)) {
            $isFile = true;
            $fileName = basename($value);
            $fileUrl = Storage::disk('public')->url($value);
            $isImage = self::isImageExtension($fileName);
            $display = $fileName;
        }

        return [
            'label' => $label,
            'type' => $type,
            'value' => (string) ($display ?? ''),
            'fileUrl' => $fileUrl,
            'fileName' => $fileName,
            'isImage' => $isImage,
            'isFile' => $isFile,
        ];
    }

    /**
     * @param  array<string, mixed>  $field
     */
    private static function resolveLabel(array $field, string $locale): string
    {
        $rawLabel = $field['label'] ?? null;
        $fallback = $field['name'] ?? $field['key'] ?? '';

        if (is_array($rawLabel)) {
            $label = $rawLabel[$locale] ?? $rawLabel['sk'] ?? reset($rawLabel) ?: $fallback;
        } else {
            $label = $rawLabel ?: $fallback;
        }

        return (string) $label;
    }

    private static function genderLabel(mixed $value): string
    {
        $enum = is_string($value) ? GenderEnum::tryFrom($value) : null;

        return $enum?->getLabel() ?? (is_scalar($value) ? (string) $value : '');
    }

    private static function formatDate(mixed $value): string
    {
        if (! is_string($value) || $value === '') {
            return is_scalar($value) ? (string) $value : '';
        }

        try {
            return Carbon::parse($value)->translatedFormat('j. F Y');
        } catch (Throwable) {
            return $value;
        }
    }

    private static function isImageExtension(string $fileName): bool
    {
        $ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        return in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'avif', 'bmp', 'svg'], true);
    }
}
