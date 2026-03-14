<?php

namespace App\Models;

use App\Enums\JoinRequestStatusEnum;
use App\Models\Concerns\HasUuidV7;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TeamJoinRequest extends Model
{
    use HasFactory, HasUuidV7;

    protected $fillable = [
        'team_id',
        'user_id',
        'name',
        'email',
        'message',
        'status',
        'processed_at',
    ];

    protected function casts(): array
    {
        return [
            'status' => JoinRequestStatusEnum::class,
            'processed_at' => 'datetime',
        ];
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isPending(): bool
    {
        return $this->status === JoinRequestStatusEnum::Pending;
    }
}
