<?php

namespace App\Models;

use App\Enums\GoalStatusEnum;
use App\Models\Concerns\HasUuidV7;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Translatable\HasTranslations;

class AthleteGoal extends Model implements HasMedia
{
    use HasFactory, HasTranslations, HasUuidV7, InteractsWithMedia;

    /** @var list<string> */
    public array $translatable = ['heading', 'description'];

    protected $fillable = [
        'user_id',
        'icon',
        'thumbnail_media_id',
        'heading',
        'description',
        'status',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'status' => GoalStatusEnum::class,
            'sort_order' => 'integer',
        ];
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('goal_media');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
