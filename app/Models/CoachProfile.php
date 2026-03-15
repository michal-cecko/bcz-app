<?php

namespace App\Models;

use App\Models\Concerns\HasUuidV7;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Translatable\HasTranslations;

class CoachProfile extends Model implements HasMedia
{
    use HasFactory, HasTranslations, HasUuidV7, InteractsWithMedia;

    /** @var list<string> */
    public array $translatable = ['biography'];

    protected $fillable = [
        'user_id',
        'date_started_coaching',
        'biography',
        'main_background_image',
        'biography_image',
    ];

    protected function casts(): array
    {
        return [
            'date_started_coaching' => 'date',
        ];
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('main_background_image')->singleFile();
        $this->addMediaCollection('biography_image')->singleFile();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
