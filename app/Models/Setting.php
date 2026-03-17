<?php

namespace App\Models;

use App\Enums\SettingTypeEnum;
use App\Models\Concerns\HasCreator;
use App\Models\Concerns\HasUuidV7;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class Setting extends Model
{
    use HasCreator, HasFactory, HasTranslations, HasUuidV7;

    /** @var list<string> */
    public array $translatable = ['label', 'description'];

    protected $fillable = [
        'key',
        'label',
        'description',
        'type',
        'options',
        'value',
        'is_exposed',
    ];

    protected function casts(): array
    {
        return [
            'type' => SettingTypeEnum::class,
            'options' => 'array',
            'value' => 'json',
            'is_exposed' => 'boolean',
        ];
    }

    public function resolvedValue(): ?string
    {
        return match ($this->type) {
            SettingTypeEnum::TEAM_SELECT => Team::find($this->value)?->name,
            SettingTypeEnum::MULTI_SELECT => is_array($this->value) ? implode(', ', $this->value) : $this->value,
            SettingTypeEnum::BOOLEAN => $this->value ? 'Áno' : 'Nie',
            default => is_array($this->value) ? json_encode($this->value) : (string) $this->value,
        };
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        $setting = static::query()->where('key', $key)->first();

        return $setting?->value ?? $default;
    }

    public static function set(string $key, mixed $value): void
    {
        static::query()->updateOrCreate(
            ['key' => $key],
            ['value' => $value],
        );
    }
}
