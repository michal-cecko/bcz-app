<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;
use Spatie\Translatable\HasTranslations;

class TeamPaymentMethod extends Pivot
{
    use HasTranslations;

    protected $table = 'payment_method_team';

    public $incrementing = true;

    /** @var list<string> */
    public array $translatable = ['title', 'description', 'instructions'];

    protected $fillable = [
        'payment_method_id',
        'team_id',
        'title',
        'description',
        'instructions',
        'is_enabled',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'is_enabled' => 'boolean',
            'sort_order' => 'integer',
        ];
    }
}
