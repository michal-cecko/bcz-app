<?php

namespace App\Services;

use App\Models\TeamSeason;
use App\Models\Training;
use Illuminate\Support\Collection;

class TrainingSeasonService
{
    /**
     * Copy trainings from a previous season to a new season.
     *
     * @param  Collection<int, string>  $trainingIds
     * @return Collection<int, Training>
     */
    public static function copyTrainingsToSeason(Collection $trainingIds, TeamSeason $newSeason): Collection
    {
        $originals = Training::query()
            ->whereIn('id', $trainingIds)
            ->with('coaches')
            ->get();

        $copies = collect();

        foreach ($originals as $original) {
            $copy = $original->replicate([
                'slug',
                'created_at',
                'updated_at',
                'deleted_at',
            ]);

            $copy->team_season_id = $newSeason->id;
            $copy->is_active = true;
            $copy->save();

            // Copy coach relationships
            $coachData = $original->coaches->mapWithKeys(fn ($coach) => [
                $coach->id => ['role' => $coach->pivot->role],
            ])->toArray();

            $copy->coaches()->sync($coachData);

            $copies->push($copy);
        }

        return $copies;
    }

    /**
     * Get trainings from a season that are marked for auto-copy.
     *
     * @return Collection<int, Training>
     */
    public static function getRecurringTrainings(TeamSeason $season): Collection
    {
        return $season->trainings()
            ->where('is_recurring_across_seasons', true)
            ->where('is_active', true)
            ->get();
    }
}
