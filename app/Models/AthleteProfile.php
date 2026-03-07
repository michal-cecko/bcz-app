<?php

namespace App\Models;

use App\Models\Concerns\HasUuidV7;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Translatable\HasTranslations;

class AthleteProfile extends Model
{
    use HasFactory, HasTranslations, HasUuidV7;

    /** @var list<string> */
    public array $translatable = ['journey_text'];

    protected $fillable = [
        'user_id',
        'date_started_working_out',
        'journey_text',
        'journey_image',
        'main_image',
    ];

    protected function casts(): array
    {
        return [
            'date_started_working_out' => 'date',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
