<?php

namespace App\Models;

use App\Contracts\Linkable;
use App\Models\Concerns\HasCreator;
use App\Models\Concerns\HasUuidV7;
use Database\Factories\MediaItemFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Collection;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class MediaItem extends Model implements HasMedia, Linkable
{
    /** @use HasFactory<MediaItemFactory> */
    use HasCreator, HasFactory, HasUuidV7, InteractsWithMedia;

    protected $fillable = [
        'team_id',
        'name',
        'description',
    ];

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('file')->singleFile();
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public function getLinkUrl(): string
    {
        return $this->getFirstMediaUrl('file');
    }

    public function getLinkLabel(): string
    {
        return $this->name;
    }

    public static function linkableOptions(): Collection
    {
        return static::query()
            ->orderBy('name')
            ->get()
            ->mapWithKeys(fn (MediaItem $m) => [$m->id => $m->name]);
    }
}
