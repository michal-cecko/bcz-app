<?php

namespace App\Models;

use App\Models\Concerns\HasUuidV7;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Translatable\HasTranslations;

class AthleteProfile extends Model implements HasMedia
{
    use HasFactory, HasTranslations, HasUuidV7, InteractsWithMedia;

    /** @var list<string> */
    public array $translatable = ['journey_text'];

    protected $fillable = [
        'user_id',
        'date_started_working_out',
        'journey_text',
        'journey_image',
        'main_image',
    ];

    protected function casts(): array
    {
        return [
            'date_started_working_out' => 'date',
        ];
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('journey_image')->singleFile();
        $this->addMediaCollection('main_image')->singleFile();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
