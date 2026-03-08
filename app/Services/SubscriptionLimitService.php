<?php

namespace App\Services;

use App\Models\Team;

class SubscriptionLimitService
{
    public function canAddMember(Team $team): bool
    {
        $limit = $this->getLimit($team, 'max_members');

        return $limit === null || $this->getUsage($team, 'max_members') < $limit;
    }

    public function canCreateTraining(Team $team): bool
    {
        $limit = $this->getLimit($team, 'max_trainings');

        return $limit === null || $this->getUsage($team, 'max_trainings') < $limit;
    }

    public function canCreateCompetition(Team $team): bool
    {
        $limit = $this->getLimit($team, 'max_competitions_yearly');

        return $limit === null || $this->getUsage($team, 'max_competitions_yearly') < $limit;
    }

    public function canCreateEvent(Team $team): bool
    {
        $limit = $this->getLimit($team, 'max_events_yearly');

        return $limit === null || $this->getUsage($team, 'max_events_yearly') < $limit;
    }

    public function getRemainingQuota(Team $team, string $limitKey): ?int
    {
        $limit = $this->getLimit($team, $limitKey);

        if ($limit === null) {
            return null;
        }

        return max(0, $limit - $this->getUsage($team, $limitKey));
    }

    public function getUsage(Team $team, string $limitKey): int
    {
        return match ($limitKey) {
            'max_members' => $team->members()->count(),
            'max_trainings' => $team->trainings()->where('is_active', true)->count(),
            'max_competitions_yearly' => $team->organizedCompetitions()
                ->whereYear('created_at', now()->year)
                ->count(),
            'max_events_yearly' => $team->events()
                ->whereYear('created_at', now()->year)
                ->count(),
            default => 0,
        };
    }

    private function getLimit(Team $team, string $limitKey): ?int
    {
        $subscription = $team->currentSubscription;

        if (! $subscription) {
            return null;
        }

        return $subscription->plan?->getLimit($limitKey);
    }
}
