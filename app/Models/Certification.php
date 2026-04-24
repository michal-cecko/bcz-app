<?php

namespace App\Models;

use App\Models\Concerns\HasUuidV7;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Spatie\Translatable\HasTranslations;

class Certification extends Model
{
    use HasFactory, HasTranslations, HasUuidV7;

    /** @var list<string> */
    public array $translatable = ['name', 'description'];

    protected $fillable = [
        'certifiable_id',
        'certifiable_type',
        'name',
        'description',
        'icon',
        'year_of_issue',
        'pdf',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'year_of_issue' => 'integer',
            'sort_order' => 'integer',
        ];
    }

    public function certifiable(): MorphTo
    {
        return $this->morphTo();
    }
}
