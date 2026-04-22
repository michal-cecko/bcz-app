<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\MorphPivot;
use Spatie\Translatable\HasTranslations;

class PayablePaymentMethod extends MorphPivot
{
    use HasTranslations;

    protected $table = 'payable_payment_method';

    public $incrementing = true;

    /** @var list<string> */
    public array $translatable = ['title', 'description', 'instructions'];

    protected $fillable = [
        'payment_method_id',
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
