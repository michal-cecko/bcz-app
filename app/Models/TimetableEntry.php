<?php

namespace App\Models;

use App\Enums\TimetableEntryStatusEnum;
use App\Models\Concerns\HasUuidV7;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Translatable\HasTranslations;

class TimetableEntry extends Model
{
    use HasFactory, HasTranslations, HasUuidV7;

    /** @var list<string> */
    public array $translatable = ['title'];

    protected $fillable = [
        'competition_id',
        'title',
        'scheduled_time',
        'actual_start_time',
        'actual_end_time',
        'status',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'scheduled_time' => 'datetime',
            'actual_start_time' => 'datetime',
            'actual_end_time' => 'datetime',
            'status' => TimetableEntryStatusEnum::class,
            'sort_order' => 'integer',
        ];
    }

    public function competition(): BelongsTo
    {
        return $this->belongsTo(Competition::class);
    }
}
