<?php

namespace App\Models;

use App\Enums\SponsorTagEnum;
use App\Models\Concerns\HasUuidV7;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sponsor extends Model
{
    use HasFactory, HasUuidV7;

    protected $fillable = [
        'name',
        'tag',
        'logo',
        'link',
        'is_visible',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'tag' => SponsorTagEnum::class,
            'is_visible' => 'boolean',
            'sort_order' => 'integer',
        ];
    }
}
