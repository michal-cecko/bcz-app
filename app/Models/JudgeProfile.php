<?php

namespace App\Models;

use App\Enums\DraftStatusEnum;
use App\Models\Concerns\HasUuidV7;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Translatable\HasTranslations;

class JudgeProfile extends Model implements HasMedia
{
    use HasFactory, HasTranslations, HasUuidV7, InteractsWithMedia;

    /** @var list<string> */
    public array $translatable = ['biography'];

    protected $fillable = [
        'user_id',
        'biography',
        'disciplines',
        'date_started_judging',
        'draft_data',
        'draft_status',
        'draft_rejection_reason',
        'draft_submitted_at',
    ];

    protected function casts(): array
    {
        return [
            'date_started_judging' => 'date',
            'disciplines' => 'array',
            'draft_data' => 'json',
            'draft_status' => DraftStatusEnum::class,
            'draft_submitted_at' => 'datetime',
        ];
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('hero_image')->singleFile();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
