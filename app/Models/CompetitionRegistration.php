<?php

namespace App\Models;

use App\Models\Concerns\HasUuidV7;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CompetitionRegistration extends Model
{
    use HasFactory, HasUuidV7;

    protected $fillable = [
        'competition_id',
        'user_id',
        'athlete_category_id',
        'registration_fee_id',
        'status',
        'registered_at',
        'form_data',
        'weight_in',
    ];

    protected function casts(): array
    {
        return [
            'registered_at' => 'datetime',
            'form_data' => 'json',
            'weight_in' => 'decimal:2',
        ];
    }

    public function competition(): BelongsTo
    {
        return $this->belongsTo(Competition::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function athleteCategory(): BelongsTo
    {
        return $this->belongsTo(AthleteCategory::class);
    }

    public function registrationFee(): BelongsTo
    {
        return $this->belongsTo(RegistrationFee::class);
    }
}
