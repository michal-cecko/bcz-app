<?php

namespace App\Models;

use App\Enums\PlanTierEnum;
use App\Models\Concerns\HasUuidV7;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;
use Spatie\Translatable\HasTranslations;

class SubscriptionPlan extends Model
{
    use HasFactory, HasSlug, HasTranslations, HasUuidV7;

    /** @var list<string> */
    public array $translatable = ['name', 'description', 'features'];

    protected $fillable = [
        'name',
        'slug',
        'tier',
        'description',
        'features',
        'limits',
        'stripe_product_id',
        'is_active',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'tier' => PlanTierEnum::class,
            'limits' => 'json',
            'is_active' => 'boolean',
        ];
    }

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom(fn (SubscriptionPlan $model) => $model->getTranslation('name', 'sk'))
            ->saveSlugsTo('slug');
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(TeamSubscription::class);
    }

    public function prices(): HasMany
    {
        return $this->hasMany(SubscriptionPlanPrice::class);
    }

    public function getLimit(string $key): ?int
    {
        if (! $this->limits) {
            return null;
        }

        return $this->limits[$key] ?? null;
    }

    public function getPriceForCurrency(string $code, string $period = 'monthly'): ?float
    {
        $price = $this->prices()->where('currency_code', $code)->first();

        if (! $price) {
            return null;
        }

        return $period === 'yearly' ? (float) $price->price_yearly : (float) $price->price_monthly;
    }
}
