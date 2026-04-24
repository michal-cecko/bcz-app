<?php

namespace App\Models;

use App\Enums\PaymentMethodEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Spatie\Translatable\HasTranslations;

class PaymentMethod extends Model
{
    use HasTranslations;

    /** @var list<string> */
    public array $translatable = ['title', 'description', 'instructions'];

    protected $fillable = [
        'method',
        'title',
        'description',
        'instructions',
        'icon',
        'is_active',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'method' => PaymentMethodEnum::class,
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function teams(): BelongsToMany
    {
        return $this->belongsToMany(Team::class)
            ->withPivot('is_enabled', 'sort_order')
            ->withTimestamps();
    }
}
