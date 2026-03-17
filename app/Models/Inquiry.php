<?php

namespace App\Models;

use App\Enums\InquiryReasonEnum;
use App\Enums\InquiryStatusEnum;
use App\Models\Concerns\HasCreator;
use App\Models\Concerns\HasUuidV7;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Inquiry extends Model
{
    use HasCreator, HasFactory, HasUuidV7;

    protected $fillable = [
        'team_id',
        'name',
        'email',
        'phone',
        'message',
        'reason',
        'status',
        'internal_note',
    ];

    protected function casts(): array
    {
        return [
            'reason' => InquiryReasonEnum::class,
            'status' => InquiryStatusEnum::class,
        ];
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }
}
