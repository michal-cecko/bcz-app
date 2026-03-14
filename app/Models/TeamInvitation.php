<?php

namespace App\Models;

use App\Enums\InvitationStatusEnum;
use App\Models\Concerns\HasUuidV7;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TeamInvitation extends Model
{
    use HasFactory, HasUuidV7;

    protected $fillable = [
        'team_id',
        'email',
        'code',
        'status',
        'invited_by',
        'accepted_at',
        'expires_at',
    ];

    protected function casts(): array
    {
        return [
            'status' => InvitationStatusEnum::class,
            'accepted_at' => 'datetime',
            'expires_at' => 'datetime',
        ];
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public function invitedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'invited_by');
    }

    public function isPending(): bool
    {
        return $this->status === InvitationStatusEnum::Pending;
    }

    public function isExpired(): bool
    {
        return $this->expires_at->isPast() || $this->status === InvitationStatusEnum::Expired;
    }
}
