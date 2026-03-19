<?php

namespace App\Models;

use App\Enums\BannerTypeEnum;
use App\Models\Concerns\HasUuidV7;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Spatie\Translatable\HasTranslations;

class Banner extends Model
{
    use HasFactory, HasTranslations, HasUuidV7, SoftDeletes;

    /** @var list<string> */
    public array $translatable = ['name'];

    protected $fillable = [
        'name', 'type', 'placement', 'page_ids', 'content',
        'is_active', 'active_from', 'active_to', 'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'type' => BannerTypeEnum::class,
            'content' => 'array',
            'page_ids' => 'array',
            'is_active' => 'boolean',
            'active_from' => 'datetime',
            'active_to' => 'datetime',
        ];
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query
            ->where('is_active', true)
            ->where(function (Builder $q) {
                $q->whereNull('active_from')->orWhere('active_from', '<=', now());
            })
            ->where(function (Builder $q) {
                $q->whereNull('active_to')->orWhere('active_to', '>=', now());
            });
    }

    public function scopeForPage(Builder $query, ?string $pageId): Builder
    {
        return $query->where(function (Builder $q) use ($pageId) {
            $q->where('placement', 'all');

            if ($pageId && Str::isUuid($pageId)) {
                $q->orWhere(function (Builder $inner) use ($pageId) {
                    $inner->where('placement', 'specific')
                        ->whereJsonContains('page_ids', $pageId);
                });
            }
        });
    }

    public function scopeOfType(Builder $query, BannerTypeEnum $type): Builder
    {
        return $query->where('type', $type);
    }

    public static function resolve(BannerTypeEnum $type, ?string $pageId): ?self
    {
        return static::query()
            ->active()
            ->forPage($pageId)
            ->ofType($type)
            ->orderByDesc('sort_order')
            ->first();
    }
}
