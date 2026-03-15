<?php

namespace App\Models;

use App\Models\Concerns\HasUuidV7;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CompetitionDetail extends Model
{
    use HasFactory, HasUuidV7;

    protected $fillable = [
        'event_id',
    ];

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function athleteCategories(): BelongsToMany
    {
        return $this->belongsToMany(AthleteCategory::class, 'competition_athlete_category');
    }

    public function disciplines(): BelongsToMany
    {
        return $this->belongsToMany(Discipline::class, 'competition_discipline');
    }

    public function judges(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'competition_judges')
            ->withPivot('discipline_id');
    }

    public function timetableEntries(): HasMany
    {
        return $this->hasMany(TimetableEntry::class)->orderBy('sort_order');
    }

    public function rounds(): HasMany
    {
        return $this->hasMany(CompetitionRound::class)->orderBy('sort_order');
    }

    public function registrationFees(): HasMany
    {
        return $this->hasMany(RegistrationFee::class);
    }
}
