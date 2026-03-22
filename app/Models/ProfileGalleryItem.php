<?php

namespace App\Models;

use App\Enums\ProfileTypeEnum;
use App\Models\Concerns\HasUuidV7;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Translatable\HasTranslations;

class ProfileGalleryItem extends Model implements HasMedia
{
    use HasFactory, HasTranslations, HasUuidV7, InteractsWithMedia;

    /** @var list<string> */
    public array $translatable = ['description'];

    protected $fillable = [
        'user_id',
        'profile_type',
        'description',
        'tags',
        'sort_order',
        'is_approved',
    ];

    protected function casts(): array
    {
        return [
            'tags' => 'array',
            'profile_type' => ProfileTypeEnum::class,
            'sort_order' => 'integer',
            'is_approved' => 'boolean',
        ];
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('image')->singleFile();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
