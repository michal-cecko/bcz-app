<?php

namespace App\Models;

use App\Enums\MenuLocationEnum;
use App\Models\Concerns\HasUuidV7;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class Menu extends Model
{
    use HasFactory, HasTranslations, HasUuidV7;

    /** @var list<string> */
    public array $translatable = ['label'];

    protected $fillable = [
        'location', 'label', 'items',
    ];

    protected function casts(): array
    {
        return [
            'location' => MenuLocationEnum::class,
            'items' => 'array',
        ];
    }
}
